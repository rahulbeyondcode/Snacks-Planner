<?php

namespace App\Services;

use App\Models\OfficeHoliday;
use App\Repositories\OfficeHolidayRepositoryInterface;


class OfficeHolidayService implements OfficeHolidayServiceInterface
{
    public function updateHoliday($id, array $data)
    {
        return $this->officeHolidayRepository->update($id, $data);
    }

    public function deleteHoliday($id)
    {
        return $this->officeHolidayRepository->delete($id);
    }
    public function getAllHolidays()
    {
        return $this->officeHolidayRepository->getAll();
    }
    protected $officeHolidayRepository;

    public function __construct(OfficeHolidayRepositoryInterface $officeHolidayRepository)
    {
        $this->officeHolidayRepository = $officeHolidayRepository;
    }

    public function setHoliday(array $data)
    {
        return $this->officeHolidayRepository->create($data);
    }

    public function createHoliday(array $data)
    {
        return $this->officeHolidayRepository->create($data);
    }

    public function isHolidaySet(string $holidayDate)
    {
        return $this->officeHolidayRepository->findByDate($holidayDate) !== null;
    }

    public function getOfficeHolidays()
    {
        return $this->officeHolidayRepository->getByType(OfficeHoliday::TYPE_OFFICE_HOLIDAY);
    }

    public function getNoSnacksDaysForGroup(int $groupId, ?int $year = null, ?int $month = null)
    {
        if ($year && $month) {
            return $this->officeHolidayRepository->getByTypeAndGroupForMonth(
                OfficeHoliday::TYPE_NO_SNACKS_DAY,
                $groupId,
                $year,
                $month
            );
        }

        return $this->officeHolidayRepository->getByTypeAndGroup(
            OfficeHoliday::TYPE_NO_SNACKS_DAY,
            $groupId
        );
    }

    public function isHolidaySetForTypeAndGroup(string $holidayDate, string $type, ?int $groupId = null)
    {
        return $this->officeHolidayRepository->findByDateTypeAndGroup($holidayDate, $type, $groupId) !== null;
    }
}