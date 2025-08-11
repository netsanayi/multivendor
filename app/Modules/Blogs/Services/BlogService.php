<?php

namespace App\Modules\Blogs\Services;

use App\Modules\Blogs\Models\Blog;
use App\Modules\Uploads\Services\UploadService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class BlogService
{
    /**
     * @var UploadService
     */
    private $uploadService;

    /**
     * Constructor
     */
    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    /**
     * Get all blogs with filters
     *
     * @param array $filters
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllBlogs(array $filters = [], int $perPage = 10)
    {
        $query = Blog::query()->with(['author', 'category']);

        // Apply filters
        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['is_featured'])) {
            $query->where('is_featured', $filters['is_featured']);
        }

        if (isset($filters['is_published'])) {
            $query->where('is_published', $filters['is_published']);
        }

        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (isset($filters['author_id'])) {
            $query->where('author_id', $filters['author_id']);
        }

        if (isset($filters['from_date'])) {
            $query->whereDate('created_at', '>=', $filters['from_date']);
        }

        if (isset($filters['to_date'])) {
            $query->whereDate('created_at', '<=', $filters['to_date']);
        }

        // Apply sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($perPage);
    }

    /**
     * Get published blogs for frontend
     *
     * @param array $filters
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getPublishedBlogs(array $filters = [], int $perPage = 12)
    {
        $query = Blog::query()
            ->with(['author', 'category'])
            ->where('is_published', true)
            ->where('status', 'published')
            ->where(function ($q) {
                $q->whereNull('published_at')
                  ->orWhere('published_at', '<=', now());
            });

        // Apply filters
        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (isset($filters['tag'])) {
            $query->whereJsonContains('tags', $filters['tag']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        // Apply sorting
        if (isset($filters['featured_first']) && $filters['featured_first']) {
            $query->orderBy('is_featured', 'desc');
        }

        $query->orderBy('published_at', 'desc');

        return $query->paginate($perPage);
    }

    /**
     * Get featured blogs
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getFeaturedBlogs(int $limit = 5)
    {
        return Cache::remember('featured_blogs', 3600, function () use ($limit) {
            return Blog::where('is_featured', true)
                ->where('is_published', true)
                ->where('status', 'published')
                ->orderBy('published_at', 'desc')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get popular blogs
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPopularBlogs(int $limit = 5)
    {
        return Cache::remember('popular_blogs', 3600, function () use ($limit) {
            return Blog::where('is_published', true)
                ->where('status', 'published')
                ->orderBy('views', 'desc')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get related blogs
     *
     * @param Blog $blog
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRelatedBlogs(Blog $blog, int $limit = 4)
    {
        $query = Blog::where('id', '!=', $blog->id)
            ->where('is_published', true)
            ->where('status', 'published');

        // Aynı kategorideki blogları öncelikli getir
        if ($blog->category_id) {
            $query->where('category_id', $blog->category_id);
        }

        // Eğer yeterli blog yoksa, benzer tag'lere sahip blogları getir
        $blogs = $query->limit($limit)->get();
        
        if ($blogs->count() < $limit && !empty($blog->tags)) {
            $additionalBlogs = Blog::where('id', '!=', $blog->id)
                ->where('is_published', true)
                ->where('status', 'published')
                ->whereNotIn('id', $blogs->pluck('id'))
                ->where(function ($q) use ($blog) {
                    foreach ($blog->tags as $tag) {
                        $q->orWhereJsonContains('tags', $tag);
                    }
                })
                ->limit($limit - $blogs->count())
                ->get();
            
            $blogs = $blogs->concat($additionalBlogs);
        }

        return $blogs;
    }

    /**
     * Create a new blog
     *
     * @param array $data
     * @return Blog
     */
    public function createBlog(array $data): Blog
    {
        return DB::transaction(function () use ($data) {
            // Handle featured image upload
            if (isset($data['featured_image'])) {
                $upload = $this->uploadService->uploadFile(
                    $data['featured_image'],
                    'blogs',
                    [
                        'resize' => [
                            'width' => 1200,
                            'height' => 630
                        ],
                        'thumbnail' => [
                            'width' => 400,
                            'height' => 300
                        ]
                    ]
                );
                $data['featured_image_id'] = $upload->id;
                unset($data['featured_image']);
            }

            // Process tags
            if (isset($data['tags']) && is_string($data['tags'])) {
                $data['tags'] = array_map('trim', explode(',', $data['tags']));
            }

            // Create blog
            $blog = Blog::create($data);

            // Clear cache
            $this->clearCache();

            return $blog;
        });
    }

    /**
     * Update a blog
     *
     * @param Blog $blog
     * @param array $data
     * @return Blog
     */
    public function updateBlog(Blog $blog, array $data): Blog
    {
        return DB::transaction(function () use ($blog, $data) {
            // Handle featured image upload
            if (isset($data['featured_image'])) {
                // Delete old image if exists
                if ($blog->featured_image_id) {
                    $this->uploadService->deleteFile($blog->featured_image_id);
                }

                $upload = $this->uploadService->uploadFile(
                    $data['featured_image'],
                    'blogs',
                    [
                        'resize' => [
                            'width' => 1200,
                            'height' => 630
                        ],
                        'thumbnail' => [
                            'width' => 400,
                            'height' => 300
                        ]
                    ]
                );
                $data['featured_image_id'] = $upload->id;
                unset($data['featured_image']);
            }

            // Process tags
            if (isset($data['tags']) && is_string($data['tags'])) {
                $data['tags'] = array_map('trim', explode(',', $data['tags']));
            }

            // Update blog
            $blog->update($data);

            // Clear cache
            $this->clearCache();

            return $blog->fresh();
        });
    }

    /**
     * Delete a blog
     *
     * @param Blog $blog
     * @return bool
     */
    public function deleteBlog(Blog $blog): bool
    {
        return DB::transaction(function () use ($blog) {
            // Delete featured image if exists
            if ($blog->featured_image_id) {
                $this->uploadService->deleteFile($blog->featured_image_id);
            }

            // Delete blog
            $deleted = $blog->delete();

            // Clear cache
            $this->clearCache();

            return $deleted;
        });
    }

    /**
     * Toggle blog featured status
     *
     * @param Blog $blog
     * @return Blog
     */
    public function toggleFeatured(Blog $blog): Blog
    {
        $blog->update(['is_featured' => !$blog->is_featured]);
        $this->clearCache();
        return $blog;
    }

    /**
     * Toggle blog published status
     *
     * @param Blog $blog
     * @return Blog
     */
    public function togglePublished(Blog $blog): Blog
    {
        $blog->update([
            'is_published' => !$blog->is_published,
            'published_at' => !$blog->is_published ? now() : $blog->published_at
        ]);
        $this->clearCache();
        return $blog;
    }

    /**
     * Increment blog views
     *
     * @param Blog $blog
     * @return void
     */
    public function incrementViews(Blog $blog): void
    {
        $blog->increment('views');
    }

    /**
     * Get all tags
     *
     * @return array
     */
    public function getAllTags(): array
    {
        return Cache::remember('blog_tags', 3600, function () {
            $tags = [];
            Blog::where('is_published', true)
                ->whereNotNull('tags')
                ->pluck('tags')
                ->each(function ($blogTags) use (&$tags) {
                    if (is_array($blogTags)) {
                        $tags = array_merge($tags, $blogTags);
                    }
                });
            
            return array_unique($tags);
        });
    }

    /**
     * Get blog statistics
     *
     * @return array
     */
    public function getStatistics(): array
    {
        return Cache::remember('blog_statistics', 3600, function () {
            return [
                'total' => Blog::count(),
                'published' => Blog::where('is_published', true)->count(),
                'draft' => Blog::where('status', 'draft')->count(),
                'featured' => Blog::where('is_featured', true)->count(),
                'total_views' => Blog::sum('views'),
                'this_month' => Blog::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count(),
                'popular_tags' => $this->getPopularTags(10)
            ];
        });
    }

    /**
     * Get popular tags with count
     *
     * @param int $limit
     * @return array
     */
    private function getPopularTags(int $limit = 10): array
    {
        $tagCounts = [];
        
        Blog::where('is_published', true)
            ->whereNotNull('tags')
            ->pluck('tags')
            ->each(function ($tags) use (&$tagCounts) {
                if (is_array($tags)) {
                    foreach ($tags as $tag) {
                        if (!isset($tagCounts[$tag])) {
                            $tagCounts[$tag] = 0;
                        }
                        $tagCounts[$tag]++;
                    }
                }
            });
        
        arsort($tagCounts);
        return array_slice($tagCounts, 0, $limit, true);
    }

    /**
     * Clear blog cache
     *
     * @return void
     */
    private function clearCache(): void
    {
        Cache::forget('featured_blogs');
        Cache::forget('popular_blogs');
        Cache::forget('blog_tags');
        Cache::forget('blog_statistics');
    }
}
