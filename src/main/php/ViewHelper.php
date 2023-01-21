<?php
/**
 * Module Libraries: basic class
 *
 * PHP version 7
 *
 * Copyright (C) Staats- und Universitätsbibliothek Hamburg 2018.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @category VuFind
 * @package  View_Helpers
 * @author   Hajo Seng <hajo.seng@sub.uni-hamburg.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/beluga-core
 */

namespace SUBHH\VuFind\SolrMarc;

use Laminas\View\Helper\AbstractHelper;

/**
 * SolrDetails helpers
 *
 * @category VuFind
 * @package  View_Helpers
 * @author   Hajo Seng <hajo.seng@sub.uni-hamburg.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development Wiki
 */
class ViewHelper extends AbstractHelper
{
    use \VuFind\View\Helper\Root\ClassBasedTemplateRendererTrait;

    /**
     * Get the core field entries
     *
     * @param SolrMarc $driver     RecordDriver to use
     * @param string[] $categories Categories to read (see config)
     *
     * @return mixed[]
     */
    public function getCoreFields(SolrMarc $driver, $categories = []) : array
    {
        $solrMarcData = [];
        if (empty($categories)) {
            $categories = [[]];
        }
        foreach ($categories as $category) {
            foreach ($driver->getSolrMarcKeys($category) as $solrMarcKey) {
                $solrMarcData[$solrMarcKey] = $driver->getMarcData($solrMarcKey);
                if (empty($solrMarcData[$solrMarcKey])) {
                    continue;
                }
                $matchKey = $solrMarcData[$solrMarcKey]['match-key'] ?? '';
                unset($solrMarcData[$solrMarcKey]['match-key']);
                if (!empty($matchKey)) {
                    foreach ($solrMarcData[$solrMarcKey] as $index => $data) {
                        if (!empty($data[$matchKey]['data'][0])) {
                            foreach ($solrMarcData[$solrMarcKey] as $index2 => $data2) {
                                if ($index2 > $index && !empty($data2[$matchKey]['data'][0]) && $data2[$matchKey]['data'][0] == $data[$matchKey]['data'][0]) {
                                    foreach ($solrMarcData[$solrMarcKey][$index2] as $name => $entry) {
                                        $solrMarcData[$solrMarcKey][$index][$name] = $entry;
                                    }
                                    unset($solrMarcData[$solrMarcKey][$index2]);
                                }
                            }
                        }
                    }
                }
                $originalLetters = '';
                foreach ($solrMarcData[$solrMarcKey] as $data) {
                    if (is_array($data)) {
                        foreach ($data as $date) {
                            if (isset($date['originalLetters'])) {
                                $originalLetters .= ' ' . $date['originalLetters'];
                            }
                        }
                    }
                }
                if (!empty($originalLetters)) {
                    $solrMarcData[$solrMarcKey]['originalLetters'] = $originalLetters;
                }
            }
        }
        return $solrMarcData;
    }

    /** @return mixed[] */
    public function getResultListLine(SolrMarc $driver) : array
    {
        $resultList = [];
        $resultListData = $driver->getMarcData('ResultList');
        if (is_array($resultListData)) {
            foreach ($resultListData as $resultListDate) {
                if (is_array($resultListDate)) {
                    foreach ($resultListDate as $resultKey => $resultListArray) {
                        if(strpos($resultKey,"_array") !== false) {
                            if (isset($resultListArray['data'][0])) {
                                $resultList[$resultKey][] = $resultListArray['data'][0];
                            }
                        } else {
                            if (!isset($resultList[$resultKey]) && isset($resultListArray['data'][0])) {
                                $resultList[$resultKey] = $resultListArray['data'][0];
                            }
                        }
                    }
                }
            }
        }
        if (empty($resultList['title'])) {
            if ($view = $this->getView()) {
                $resultList['title'] = $view->transEsc('no title');
            }
        }
        return $resultList;
    }
}
