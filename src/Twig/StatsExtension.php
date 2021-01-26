<?php
/**
 * Stats Gathering Application
 *
 * @copyright  Copyright (C) WebSpark, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt GNU General Public License Version 3
 */

namespace Mautic\StatsBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class StatsExtension
 */
class StatsExtension extends AbstractExtension
{
    /**
     * Array containing the colors to use in charts
     *
     * @var array
     */
    private $colors = [
        1 => '#7281bd',
        2 => '#4e5e9e',
        3 => '#3d4c86',
        4 => '#269ea2',
        5 => '#35b5b9',
        6 => '#fdb933',
        7 => '#63c29d',
        8 => '#00b49d',
        9 => '#ffcb64',
    ];

    /**
     * Array containing the keys for the used colors
     *
     * @var array
     */
    private $usedColors = [];

    /**
     * {@inheritdoc}
     *
     * @return TwigFunction[] An array of functions
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('chart_color', [$this, 'getChartColor']),
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'stats';
    }

    /**
     * Retrieves a color for the chart
     *
     * @return string
     */
    public function getChartColor()
    {
        $color = '';

        // First, check if all the colors have been used
        if (count($this->colors) === count($this->usedColors)) {
            $this->usedColors = [];
        }

        $found = false;

        while (!$found) {
            $key = rand(1, count($this->colors));

            if (!array_key_exists($key, $this->usedColors)) {
                // Get the color to return
                $color = $this->colors[$key];

                // Breaks the loop
                $found = true;

                // Flags the color as used
                $this->usedColors[$key] = true;
            }
        }

        return $color;
    }
}
