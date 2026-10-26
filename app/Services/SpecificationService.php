<?php

namespace App\Services;

use App\DTO\Specification\SpecificationData;
use App\DTO\Specification\SpecificationGroupData;
use App\DTO\Specification\SpecificationCategoryFilterData;
use App\Models\Category;
use App\Models\Specification;
use App\Models\SpecificationGroup;
use App\Services\Contracts\SpecificationServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Validation\ValidationException;

class SpecificationService implements SpecificationServiceInterface
{
    public function categories(SpecificationCategoryFilterData $filters): Paginator
    {
        return Category::specificationCategories($filters);
    }

    public function category(int $categoryId): Category
    {
        return Category::details($categoryId);
    }

    public function groups(int $categoryId): Collection
    {
        return SpecificationGroup::forCategory($categoryId);
    }

    public function createGroup(int $categoryId, SpecificationGroupData $data): SpecificationGroup
    {
        return SpecificationGroup::createForCategory($categoryId, $data);
    }

    public function updateGroup(int $categoryId, int $groupId, SpecificationGroupData $data): SpecificationGroup
    {
        return SpecificationGroup::updateForCategory($categoryId, $groupId, $data);
    }

    public function deleteGroup(int $categoryId, int $groupId): void
    {
        SpecificationGroup::deleteForCategory($categoryId, $groupId);
    }

    public function createSpecification(int $categoryId, SpecificationData $data): Specification
    {
        return Specification::createForCategory($categoryId, $data, $this->parseOptions($data));
    }

    public function specification(int $categoryId, int $specificationId): Specification
    {
        return Specification::detailsForCategory($categoryId, $specificationId);
    }

    public function updateSpecification(int $categoryId, int $specificationId, SpecificationData $data): Specification
    {
        return Specification::updateForCategory($categoryId, $specificationId, $data, $this->parseOptions($data));
    }

    public function deleteSpecification(int $categoryId, int $specificationId): void
    {
        Specification::deleteForCategory($categoryId, $specificationId);
    }

    /** @return array<string, string> */
    private function parseOptions(SpecificationData $data): array
    {
        $lines = preg_split('/\R/u', trim($data->options ?? '')) ?: [];
        $options = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            [$value, $label] = array_pad(explode('|', $line, 2), 2, '');
            $value = trim($value);
            $label = trim($label);

            if ($value === '' || $label === '' || isset($options[$value])) {
                throw ValidationException::withMessages(['options' => 'هر گزینه باید به صورت «کد|عنوان» و با کد یکتا در یک خط نوشته شود.']);
            }

            if (mb_strlen($value) > 255 || mb_strlen($label) > 255) {
                throw ValidationException::withMessages(['options' => 'کد و عنوان هر گزینه حداکثر ۲۵۵ نویسه باشد.']);
            }

            $options[$value] = $label;
        }

        if ($data->type->usesOptions() && $options === []) {
            throw ValidationException::withMessages(['options' => 'برای مشخصه انتخابی دست‌کم یک گزینه ثبت کنید.']);
        }

        if (! $data->type->usesOptions() && $options !== []) {
            throw ValidationException::withMessages(['options' => 'فقط مشخصه‌های انتخابی می‌توانند گزینه داشته باشند.']);
        }

        return $options;
    }
}
