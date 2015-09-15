<?php
/**
 * Stats Gathering Application
 *
 * @copyright  Copyright (C) WebSpark, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt GNU General Public License Version 3
 */

namespace StatsAppBundle\Twig;

use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class StatsExtension
 */
class StatsExtension extends \Twig_Extension
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
     * @var RequestStack
     */
    private $requestStack;

    /**
     * Array containing the keys for the used colors
     *
     * @var array
     */
    private $usedColors = [];

    /**
     * Constructor
     *
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     *
     * @return \Twig_SimpleFunction[] An array of functions
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('chart_color', [$this, 'getChartColor']),
            new \Twig_SimpleFunction('mautic_tracking', [$this, 'mauticTrackingPixel']),
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

    /**
     * Render the Mautic tracking pixel
     *
     * @return string
     */
    public function mauticTrackingPixel()
    {
        $request = $this->requestStack->getCurrentRequest();

        $currentUrl = $request->getUri();

        $attrs = [
            'title' => 'Mautic Application Statistics',
            'language' => $request->getLocale(),
            'referrer' => $request->headers->get('referer', $currentUrl),
            'url' => $currentUrl
        ];

        $trackingData = urlencode(base64_encode(serialize($attrs)));

        return '<img style="display:none" src="https://www.mautic.org/m/mtracking.gif?d='.$trackingData.'" />';
    }
}
