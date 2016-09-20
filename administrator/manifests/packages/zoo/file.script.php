<?php

jimport('joomla.database.tablenested'); // j25 fix

class pkg_zooInstallerScript extends ZooInstallerScript {

    public function postflight($type, $parent, $results) {

        $db = JFactory::getDBO();

        // make sure there is no preset content
        $db->setQuery("SELECT * FROM `#__zoo_application`")->execute();
        if ($db->getNumRows() == 0) {

            // import sql
            if (file_exists(__DIR__.'/demo.sql') and $sql = file_get_contents(__DIR__.'/demo.sql')) {

				$queries = JDatabaseDriver::splitSql($sql);

                if (count($queries) != 0) {
                    foreach ($queries as $query) {
                        $query = trim($query);

                        if ($query != '' && $query[0] != '#')
                        {
                            $db->setQuery($query);

                            if (!$db->execute()) {
                                JError::raiseWarning(1, JText::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $db->stderr(true)));
                            }
                        }
                    }
                }
            }

            // copy demo images
            if (!JFolder::exists(JPATH_ROOT . '/images/yootheme/zoo')) {
                JFolder::copy(__DIR__.'/images', JPATH_ROOT . '/images/yootheme/zoo', '', true);
            }

            // update menu item
            $zoo_menu_id  = $db->setQuery("SELECT `id` FROM `#__menu` WHERE `menutype` = 'mainmenu' AND `alias` = 'zoo-zoo'")->loadResult();
            $component_id = $db->setQuery("SELECT `extension_id` FROM `#__extensions` WHERE `name` = 'com_zoo'")->loadResult();

            if ($zoo_menu_id && $component_id) {

                // update ZOO menu
                $db->setQuery("UPDATE `#__menu` SET `component_id` = {$component_id} WHERE `menutype` = 'mainmenu' AND `alias` LIKE '%-zoo'")->execute();

                // update ZOO submenus
                $db->setQuery("UPDATE `#__menu` SET `parent_id` = {$zoo_menu_id} WHERE `menutype` = 'mainmenu' AND `alias` LIKE '%-zoo' AND `id` <> {$zoo_menu_id}")->execute();

                // rebuild
                JTableNested::getInstance('Menu', 'JTable', array())->rebuild();
            }

        } else {
            JError::raiseWarning(1, 'No DEMO Data was imported because there is already some ZOO content.');
        }

        parent::postflight($type, $parent, $results);
    }

    public function uninstall($parent) {

        $db = JFactory::getDBO();

        // remove demo images
        if (JFolder::exists(JPATH_ROOT . '/images/yootheme/zoo/')) {
            JFolder::delete(JPATH_ROOT . '/images/yootheme/zoo/');
        }

        // remove menu items
        $db->setQuery("DELETE FROM `#__menu` WHERE `menutype` = 'mainmenu' AND `alias` LIKE '%-zoo'")->execute();

        // remove modules
        $db->setQuery("DELETE FROM `#__modules` WHERE `module` LIKE 'mod_zoo%'")->execute();

        // rebuild
        JTableNested::getInstance('Menu', 'JTable', array())->rebuild();

        parent::uninstall($parent);
    }

}

class ZooInstallerScript {

	public function install($parent) {}

	public function uninstall($parent) {}

	public function update($parent) {}

	public function preflight($type, $parent) {}

	public function postflight($type, $parent, $results) {
		if (class_exists('AppRequirements')) {
			$requirements = new AppRequirements();
			$requirements->checkRequirements();
			$requirements->displayResults();
		}

		if (class_exists('App')) {
			// get zoo instance
			$app = App::getInstance('zoo');

			$app->module->enable('mod_zooquickicon', 'icon');
			$app->plugin->enable('zooshortcode');
			$app->plugin->enable('zoosmartsearch');
			$app->plugin->enable('zoosearch');
			$app->plugin->enable('zooevent');
		}

		// updateservers url update workaround
        if ('update' == $type) {

            $db = JFactory::getDBO();

            if ($parent->manifest->updateservers) {

            	$servers = $parent->manifest->updateservers->children();

                $db->setQuery(
                    "UPDATE `#__update_sites` a" .
                    " LEFT JOIN `#__update_sites_extensions` b ON b.update_site_id = a.update_site_id" .
                    " SET location = " . $db->quote(trim((string) $servers[0])) . ', enabled = 1' .
                    " WHERE b.extension_id = (SELECT `extension_id` FROM `#__extensions` WHERE `type` = 'package' AND `element` = 'pkg_widgetkit')"
                )->execute();

            }
        }

		$extensions = array();
		foreach($results as $result) {
			$extensions[] = (object) array('name' => $result['name'] == 'com_zoo' ? 'ZOO extension' : $result['name'], 'status' => $result['result'], 'message' => $result['result'] ? ($type == 'update' ? 'Updated' : 'Installed').' successfully' : 'NOT Installed');
		}

		// display extension installation results
		self::displayResults($extensions, 'Extensions', 'Extension');
	}

	protected function displayResults($result, $name, $type) { ?>

		<h3><?php echo JText::_($name); ?></h3>
		<table class="adminlist table table-bordered table-striped" width="100%">
			<thead>
				<tr>
					<th class="title"><?php echo JText::_($type); ?></th>
					<th width="60%"><?php echo JText::_('Status'); ?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="2">&nbsp;</td>
				</tr>
			</tfoot>
			<tbody>
				<?php
					foreach ($result as $i => $ext) : ?>
					<tr class="row<?php echo $i++ % 2; ?>">
						<td class="key"><?php echo $ext->name; ?></td>
						<td>
							<?php $style = $ext->status ? 'font-weight: bold; color: green;' : 'font-weight: bold; color: red;'; ?>
							<span style="<?php echo $style; ?>"><?php echo JText::_($ext->message); ?></span>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

<?php }

}