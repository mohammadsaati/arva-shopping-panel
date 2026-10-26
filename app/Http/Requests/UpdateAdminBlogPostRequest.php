<?php

namespace App\Http\Requests;

use App\Enums\BlogPostStatusEnum;
use Hekmatinasser\Verta\Verta;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAdminBlogPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $postId = (int) $this->route('postId');

        return [
            'blog_category_id' => ['required', 'integer', 'exists:blog_categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'regex:/^[\pL\pN\-_]+$/u', Rule::unique('blog_posts', 'slug')->ignore($postId)],
            'excerpt' => ['nullable', 'string', 'max:2000'],
            'body' => ['required_if:status,published', 'nullable', 'string', 'max:100000'],
            'featured_image' => ['nullable', 'image', 'max:5120'],
            'remove_featured_image' => ['nullable', 'boolean'],
            'status' => ['required', 'string', Rule::enum(BlogPostStatusEnum::class)],
            'is_featured' => ['sometimes', 'boolean'],
            'featured_order' => ['required', 'integer', 'min:0', 'max:65535'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:1000'],
            'published_at' => ['nullable', 'jdatetime:Y/m/d H:i:s'],
        ];
    }

    public function publishedAt(): ?string
    {
        if (! $this->filled('published_at')) {
            return null;
        }

        return Verta::parseFormat('Y/m/d H:i:s', $this->string('published_at')->value())
            ->toCarbon()
            ->format('Y-m-d H:i:s');
    }
}
