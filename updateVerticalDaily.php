<?php

require dirname(__FILE__) . '/Time.php';

		$date		= strtotime("-1 day", Util_Time::getToday());
		$stat_start = strtotime("2009-02-27 00:00:00");

		while ($date >= $stat_start) {
			if (Db::findOne('analytics.vertical.day', array('date' => new MongoDate($date)))) {
				// we've got the stats we need
				return;
			}

			$verticals = Util_Site::getVerticals(false, false, true);
			$totals = array_combine(
				array_map_attribute($verticals, 'name'),
				array_fill(0, count($verticals), 0)
			);
			// main vertical
			$totals[null] = 0;
			// killed verticals
			$totals['other'] = 0;

			$five_min = Db::find('analytics.vertical.fivemin', array(
				'date' => array(
					'$gte' => new MongoDate($date),
					'$lt'  => new MongoDate(strtotime("+1 day", $date))
				)
			));

			foreach ($five_min as $stat) {
				$vertical = $stat[$this->_searchFieldName];

				$vertical = $this->convertVerticalAlias($vertical);

				if (!isset($totals[$vertical]))
					$totals['other'] += $stat['n'];
				else
					$totals[$vertical] += $stat['n'];
			}

			foreach ($totals as $k => $v) {
				Db::save('analytics.vertical.day', array(
					'date'	   => new MongoDate($date),
					'vertical' => $k,
					'n'		   => $v
				));
			}

			$date = strtotime("-1 day", $date);
		}

?>