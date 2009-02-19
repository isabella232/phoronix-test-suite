<?php

/*
	Phoronix Test Suite
	URLs: http://www.phoronix.com, http://www.phoronix-test-suite.com/
	Copyright (C) 2009, Phoronix Media
	Copyright (C) 2009, Michael Larabel
	pts-includes-gtk.php: Functions needed for the GTK interface

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

function pts_gtk_add_menu($vbox, $menu)
{
	$menu_bar = new GtkMenuBar();
	$vbox->pack_start($menu_bar, false, false);

	foreach($menu as $this_menu => $sub_menu)
	{
		$new_menu = new GtkMenuItem($this_menu);
		$menu_bar->append($new_menu);
		$menu = new GtkMenu();
		$new_menu->set_submenu($menu);

		$sub_menu = pts_to_array($sub_menu);
		foreach($sub_menu as $this_object)
		{
			if($this_object == null)
			{
				$menu_item = new GtkSeparatorMenuItem();
				$menu->append($menu_item);
			}
			else if($this_object->get_type() == "CHECK_BUTTON")
			{
				$menu_item = new GtkCheckMenuItem($this_object->get_title());
				$menu_item->connect("toggled", $this_object->get_function_call());

				if($this_object->get_active_default())
				{
					$menu_item->set_active(true);
				}

				$menu->append($menu_item);
			}
			else if($this_object->get_type() == "RADIO_BUTTON")
			{
				$radio = array();
				$radio[0] = null;
				$i = 1;

				foreach($this_object->get_title() as $radio_item)
				{
					$radio[$i] = new GtkRadioMenuItem($radio[1], $radio_item);
					$radio[$i]->connect("toggled", $this_object->get_function_call());
					$menu->append($radio[$i]);
					$i++;
				}
				$radio[1]->set_active(true);
			}
			else
			{
				if($this_object->get_image() == null)
				{
					$menu_item = new GtkMenuItem($this_object->get_title());
				}
				else
				{
					$menu_item = new GtkMenuItem();
					$hbox = new GtkHBox();
					$menu_item->add($hbox);
					$img = GtkImage::new_from_stock($this_object->get_image(), Gtk::ICON_SIZE_SMALL_TOOLBAR);
					$hbox->pack_start($img, 0);
					$label = new GtkLabel($this_object->get_title());
					$hbox->pack_start($label);
				}

				$menu_item->connect("activate", $this_object->get_function_call());
				$menu->append($menu_item);
			}
		}
	}
}
function pts_gtk_add_table($headers, $data, $connect_to = null)
{
	$scrolled_window = new GtkScrolledWindow();
	$scrolled_window->set_policy(Gtk::POLICY_AUTOMATIC, Gtk::POLICY_AUTOMATIC);
	//$vbox->pack_start($scrolled_window);

	if(count($headers) == 2)
	{
		$model = new GtkListStore(GObject::TYPE_STRING, GObject::TYPE_STRING);
	}
	else
	{
		$model = new GtkListStore(GObject::TYPE_STRING);
	}

	$view = new GtkTreeView($model);
	$scrolled_window->add($view);

	for($i = 0; $i < count($headers); $i++)
	{
		$cell_renderer = new GtkCellRendererText();
		$column = new GtkTreeViewColumn($headers[$i], $cell_renderer, "text", $i);
		$view->append_column($column);
	}

	for($r = 0; $r < count($data); $r++)
	{
		$values = array();

		$data[$r] = pts_to_array($data[$r]);
		for($c = 0; $c < count($data[$r]); $c++)
		{
			array_push($values, $data[$r][$c]);
		}
		$model->append($values);
	}

	if($connect_to != null)
	{
		$selection = $view->get_selection();
		$selection->connect("changed", $connect_to);
	}

	return $scrolled_window;
}
function pts_gtk_selected_item($object)
{
	list($model, $iter) = $object->get_selected();

	return $model->get_value($iter, 0);
}
function pts_gtk_add_notebook_tab($notebook, $widget, $label)
{
	$event_box = new GtkEventBox();
	$label = new GtkLabel($label);
	$label->show();
	$event_box->add($label);
	$event_box->connect("button-press-event", array("gui_gtk", "notebook_main_page_select"), $label->get_text());
	$notebook->append_page($widget, $event_box);
}

?>
