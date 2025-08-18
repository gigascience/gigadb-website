<?php

declare(strict_types=1);

namespace GigaDB\services;

use GigaDB\models\Dataset;

class UploadStatusWorkflowService
{
    /**
     * Update a dataset's upload_status from one status to another
     *
     * If the fromStatus doesn't exist, it is noop and return false
     *
     * @param string      $fromStatus     upload status to transition from
     * @param string      $toStatus       upload status to transition to
     * @param string|null $identifier
     * @param string|null $previousStatus useful if dataset has already been updated
     *
     * @return bool whether the transition was enacted or not
     */
    public function transitionStatus(
        string $fromStatus,
        string $toStatus,
        ?int $identifier = null,
        ?string $previousStatus = null,
        bool $needToSave = false
    ): bool {
        if (!$previousStatus && !$identifier) {
            throw new \InvalidArgumentException('Both $identifier and $previousStatus cannot be null');
        }

        $dataset = null;
        if (!$previousStatus) {
            $dataset = Dataset::find()->where(['identifier' => $identifier])->one();

            if (!$dataset) {
                    throw new \InvalidArgumentException('Dataset not found');
                }
        }

        if ($fromStatus !== ($previousStatus ?: $dataset->upload_status)) {
            \Yii::log(sprintf('Failed to change status to %s', $toStatus), 'error');
            return false;
        }

        if ($needToSave && $dataset) {
            $dataset->upload_status = $toStatus;

            return $dataset->save();
        }

        return true;
    }
}
