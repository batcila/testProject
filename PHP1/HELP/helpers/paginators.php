<?php
	define ('MAX_ON_PAGE', 25, true);
	define ('MOP_PARAM', 'mop', true);

	function getPaginator($resource, $current_page = null, $max_on_page = MAX_ON_PAGE, $options = array())
	{
		Global $_connection, $source, $is_admin_user;

		$result = array(
                            'resource' => array(),
                            'paginator' => array(
                                'first' => null,
                                'prev' => null,
                                'current' => null,
                                'next' => null,
                                'last' => null
                            )
                );

        if(!isset($options['DEFAULT_LABELS']))
        {
            $options['DEFAULT_LABELS'] = array(
                'first' => '<span class="glyphicon glyphicon-step-backward" style="font-size:12px;"></span>',
                'prev' => '<span class="glyphicon glyphicon-chevron-left" style="font-size:12px;"></span>',
                'current' => '<span class="glyphicon glyphicon-chevron-down" style="font-size:12px;"></span>',
                'next' => '<span class="glyphicon glyphicon-chevron-right" style="font-size:12px;"></span>',
                'last' => '<span class="glyphicon glyphicon-step-forward" style="font-size:12px;"></span>'
            );
        }

		if (is_string($resource))
		{
			$resource = mysqli_query($_connection, $resource);
		}

		if (is_object($resource))
		{
			$resource = to_assoc_array($resource);
		}

		if(isset($source[MOP_PARAM])) // MOP = MaxOnPage :-)
		{
			$max_on_page = (int) $source[MOP_PARAM];

			if($max_on_page == 0)
			{
				$max_on_page = count($resource);
			}
		}

		if (is_array($resource))
		{
            $max_pages = (int) (count($resource) / $max_on_page);
            if (count($resource) % $max_on_page != 0)
            {
                $max_pages += 1;
            }

            if (is_null ($current_page))
            {
				$current_page = (isset($source[PAGE]) && $source[PAGE] > 0 ? $source[PAGE] : 1);
            }

            if ($current_page > $max_pages)
            {
				$current_page = $max_pages;
                //header('location:'. header_link() . '&' . PAGE . '=' .$max_pages); exit;
            }

			$resource = array_values($resource); // reset array keys
			$result['start_item'] = ($current_page - 1) * $max_on_page;
			$result['end_item'] = $result['start_item'] + $max_on_page;

            if($result['end_item'] > count($resource))
            {
                $result['end_item'] = count($resource);
            }

			for($iterator = $result['start_item']; $iterator < $result['end_item']; $iterator++)
			{
                if(isset($resource[$iterator]))
                {
                    $result['resource'][] = $resource[$iterator];
                }
			}

            $current_url = $_SERVER['REQUEST_URI'];
			$current_url = str_ireplace('&', '& ', $current_url); // Слагаме този ред, защото без него, ако имаме ключова дума например not_given тя ще създаде проблеми.
            $current_url = str_ireplace('&' . PAGE . '=' . (isset($_GET[PAGE]) ? $_GET[PAGE] : ''),'',$current_url);

			if($max_pages > 1)
			{
				// Do prev and next button
				$result['paginator']['prev'] = ($current_page > 1 ? $current_url . '&' . PAGE . '=' . ($current_page - 1) : '#');
				$result['paginator']['next'] = ($current_page < $max_pages ? $current_url . '&' . PAGE . '=' . ($current_page + 1) : '#');
			}

			if ($max_pages > 2)
			{
				# code...
                $result['paginator']['current'] = ( true ? $current_url . '&'.MOP_PARAM.'=' . count($resource) : '#');
                if(is_null($options['DEFAULT_LABELS']['current']))
                {
                    $options['DEFAULT_LABELS']['current'] = '<span style="font-size:14px;">'.$current_page.'</span>';
                }
			}

            if ($max_pages > 3)
			{
				# code...
                $result['paginator']['first'] = ($current_page > 1 ? $current_url . '&' . PAGE . '=1' : '#');
				$result['paginator']['last'] = ($current_page < $max_pages ? $current_url . '&' . PAGE . '=' . $max_pages : '#');
			}

			if($max_pages > 1)
			{
				foreach($result['paginator'] as $k => $url)
				{
					if(!is_null($url)) // Имаме такъв линк (дори и той да е #), значи генерираме хипервръзка и я показваме като бутон
					{
						$result['paginator'][$k] = '<a class="button button-pill button-action" href="' . $url . '">' . $options['DEFAULT_LABELS'][$k] . '</a>';
					}
					else // Щом не е зададен никакъв линк, значи такъв бутон не би трябвало да се показва, понеже не е минало през проверките на редове 84-106
					{
						unset($result['paginator'][$k]);
					}
				}

				$result['paginator'] = '<span class="paginator">'.implode('',$result['paginator']).'</span>';
			}
			else
			{
				$result['paginator'] = '';
			}
		}

		return $result;
	}
?>