<?php

	Class Extension_DS_Multiplier extends Extension {
		public function getSubscribedDelegates() {
			return array(
				array(
					'page' => '/backend/',
					'delegate' => 'InitaliseAdminPageHead',
					'callback' => '__appendAssets',
				),
				array(
					'page' => '/blueprints/datasources/',
					'delegate' => 'DatasourcePreCreate',
					'callback' => '__modifyFilters',
				),
				array(
					'page' => '/blueprints/datasources/',
					'delegate' => 'DatasourcePreEdit',
					'callback' => '__modifyFilters',
				),
				array(
					'page' => '/frontend/',
					'delegate' => 'DataSourcePreExecute',
					'callback' => '__dsPreExec'
				),
			);
		}

		public function __appendAssets() {
			if(class_exists('Administration')
				&& Administration::instance() instanceof Administration
				&& Administration::instance()->Page instanceof HTMLPage
			) {
				$callback = Administration::instance()->getPageCallback();
				if($callback['driver'] == 'blueprintsdatasources' 
					&& !empty($callback['context']) 
					&& ($callback['context'][0] == 'new' || $callback['context'][0] == 'edit'
				)) {
					$handle = NULL;
					if ($callback['context'][0] == 'edit' && !empty($callback['context'][1])) {
						$handle = $callback['context'][1];
					}

					// Find current Expression values.
					$multipliers = array();
					$filters = array();
					if(isset($_POST['multipliers']) && $_POST['fields']['filter']) {
						$multipliers = $_POST['multipliers'];
						foreach($_POST['fields']['filter'] as $f) {
							$filters += $f;
						}
					}
					elseif(!empty($handle)) {
						$existing = DatasourceManager::create($handle, NULL, false);
						if(!empty($existing)) {
							if(isset($existing->dsParamFILTERS)) {
								$filters = $existing->dsParamFILTERS;
							}
							if (isset($existing->dsParamMULTIPLIERS)) {
								$multipliers = $existing->dsParamMULTIPLIERS;
							}
						}
					}

					Administration::instance()->Page->addElementToHead(
						new XMLElement(
							'script',
							"Symphony.Context.add('ds_multipliers', " . json_encode($multipliers) . ");
							Symphony.Context.add('ds_filters', " . json_encode($filters) . ");",
							array('type' => 'text/javascript')
						), 100
					);

					Administration::instance()->Page->addScriptToHead(URL . '/extensions/ds_multiplier/assets/ds_multiplier.blueprints.js', 100, false);
				}
			}
		}

		public function __modifyFilters(&$context) {
			//die('hi');
			if(!isset($_POST['multipliers'])) return;

			$data = "public \$dsParamMULTIPLIERS = ";
			$data .= var_export($_POST['multipliers'], true);
			$data .= ";\n\n\t\t";

			$context['contents'] = preg_replace('/public \$dsParamFILTERS/', $data . " $0", $context['contents']);
		}

		public function __dsPreExec(&$context) {
			if(!empty($context) && isset($context['datasource']) && empty($context['xml']) && isset($context['datasource']->dsParamMULTIPLIERS)) {
				$ds = $context['datasource'];
				$container = new XMLElement($ds->dsParamROOTELEMENT);
				$section_info = null;
				
				foreach($ds->dsParamMULTIPLIERS as $key => $v) {
					$filters = explode(',', $ds->dsParamFILTERS[$key]);
					foreach($filters as $value) {
						$ds->dsParamFILTERS[$key] = $value;
						$returned = $ds->execute($context['param_pool']);

						if($returned->getNumberOfChildren() > 0) {
							if(empty($section_info)) {
								$section_info = $returned->getChild(0);
							}
							$returned->removeChildAt(0);
							if(isset($ds->dsParamGROUP)) {
								$returned = $returned->getChild(1);
							}
						}
						
						$container->appendChild($returned);
					}
				}
				$container->insertChildAt(0, $section_info);
				$context['xml'] = $container;
			}
		}
	}
