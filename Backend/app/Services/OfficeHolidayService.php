<?php

namespace App\Services;

use App\Models\OfficeHoliday;
use App\Repositories\OfficeHolidayRepositoryInterface;

class OfficeHolidayService extends BaseService implements OfficeHolidayServiceInterface
{
    public function __construct(OfficeHolidayRepositoryInterface $officeHolidayRepository)
    {
        $this->repository = $officeHolidayRepository;
    }

    public function getAllHolidays()
    {
        return $this->repository->getAll();
    }

    public function setHoliday(array $data)
    {
        return $this->create($data);
    }

    public function createHoliday(array $data)
    {
        return $this->create($data);
    }

    public function updateHoliday($id, array $data)
    {
        return $this->update($id, $data);
    }

    public function deleteHoliday($id)
    {
        return $this->delete($id);
    }

    public function isHolidaySet(string $holidayDate)
    {
        return $this->repository->findByDate($holidayDate) !== null;
    }

    public function getOfficeHolidays()
    {
        return $this->repository->getByType(OfficeHoliday::TYPE_OFFICE_HOLIDAY);
    }

    public function getNoSnacksDaysForGroup(int $groupId, ?int $year = null, ?int $month = null)
    {
        if ($year && $month) {
            return $this->repository->getByTypeAndGroupForMonth(
                OfficeHoliday::TYPE_NO_SNACKS_DAY,
                $groupId,
                $year,
                $month
            );
        }

        return $this->repository->getByTypeAndGroup(
            OfficeHoliday::TYPE_NO_SNACKS_DAY,
            $groupId
        );
    }

    public function isHolidaySetForTypeAndGroup(string $holidayDate, string $type, ?int $groupId = null)
    {
        return $this->repository->findByDateTypeAndGroup($holidayDate, $type, $groupId) !== null;
    }
}