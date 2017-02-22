<?php

namespace Limitless\Delivery\Helper;

class MetapackData implements DatepickerTransformationInterface
{
    public function addDate(array $option)
    {
        $option['delivery']['date'] = explode('T', $option['delivery']['from'])[0];

        return $option;
    }

    public function addDaysFromToday(array $option)
    {
        $option['delivery']['daysFromToday'] = $this->calculateDateDifference(date('Y-m-d'), $option['delivery']['date']);

        return $option;
    }

    public function calculateDateDifference(string $date1, string $date2)
    {
        $d1 = strtotime($date1);
        $d2 = strtotime($date2);

        $dateDifference = ($d2 - $d1) / (24 * 60 * 60);

        return (int) $dateDifference;
    }

    public function groupByDaysFromToday($options)
    {
        $groupedOptions = [];

        foreach ($options as $option) {
            $groupedOptions[$option['delivery']['daysFromToday']][] = $option;
        }

        ksort($groupedOptions);

        return $groupedOptions;
    }

    public function fillGaps($groupedOptions)
    {
        $firstDay = array_keys($groupedOptions)[0];
        $endDay = array_slice(array_keys($groupedOptions), -1)[0];

        for($i = $firstDay; $i < $endDay; $i++) {
            if(!isset($groupedOptions[$i])) {
                $groupedOptions[$i] = [];
            }
        }

        ksort($groupedOptions);

        return $groupedOptions;

    }

    public function getMaxOptionsPerDay(array $metapackData): int
    {
        return max(array_map('count', $this->getDeliveryOptions($metapackData)));
    }

    public function getDeliveryOptions(array $metapackData): array
    {
        $mpData = array_map([$this, 'addDaysFromToday'],
            array_map([$this, 'addDate'], $metapackData)
        );

        return $this->fillGaps($this->groupByDaysFromToday($mpData));
    }
}