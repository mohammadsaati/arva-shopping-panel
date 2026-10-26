<?php

namespace App\Http\Requests\Concerns;

use App\DTO\Specification\SpecificationAnswerData;

trait ReadsSpecificationAnswers
{
    /** @return array<int, SpecificationAnswerData> */
    public function specificationAnswers(): array
    {
        $answers = [];

        foreach ($this->array('specifications') as $specificationId => $value) {
            if (! is_array($value)) {
                continue;
            }

            $optionIds = $value['options'] ?? [];
            if (isset($value['option']) && $value['option'] !== '') {
                $optionIds[] = $value['option'];
            }

            $answers[] = new SpecificationAnswerData(
                specificationId: (int) $specificationId,
                text: isset($value['text']) && $value['text'] !== '' ? (string) $value['text'] : null,
                number: isset($value['number']) && $value['number'] !== '' ? (string) $value['number'] : null,
                boolean: isset($value['boolean']) && $value['boolean'] !== '' ? (bool) $value['boolean'] : null,
                optionIds: array_map('intval', is_array($optionIds) ? $optionIds : []),
            );
        }

        return $answers;
    }
}
