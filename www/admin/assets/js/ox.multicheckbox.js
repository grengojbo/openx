/**
 * A plugin for handling 2-level checkbox hierarchies. The convention
 * that defines parent-child relationships between checkboxes is that all checkboxes whose
 * id starts with "<parent-checkbox-id>_" are treated as the parent's childs.
 *
 * The utitlity comes in two variants:
 *   - multicheckbox() -- suitable for individual checkboxes (O(1)), not suitable
 *     for large numbers of checkboxes (e.g. in tables) -- large event handler 
 *     installation overhead
 *
 *   - multicheckboxes() -- uses event delegation, suitable for large numbers of
 *     checkboxes (e.g. in tables). Also provides additional support for checkboxes
 *     embedded in table rows (selecting checkbox when row is clicked, row styling
 *     dependant on previous/new checkbox state).  
 */
(function($) {
  /**
   * Treats each of the selected checkboxes as parent checkboxes and installs
   * the parent-child handler for both the parent and its children.
   *
   * Not suitable for large numbers of checkboxes, use multicheckboxes() instead. 
   */
  $.fn.multicheckbox = function() {
    return this.each(function() {
      var $parent = $(this);

      $("[@id^='" + this.id + "_']").click(function (event) {
        // Check if all checkboxes in the group are checked
        var allChecked = true;
        
        $("[@id^='" + $parent.attr("id") + "_']").each(function() {
          if (!this.checked) {
            allChecked = false;
            return false;
          }
        });
        
        $parent.attr("checked", allChecked);
        event.stopPropagation();
      }); 

      $parent.click(function(event) {
        $("[@id^='" + this.id + "_']").attr("checked", this.checked);
        event.stopPropagation();
      });
    });
  };

  /**
   * Installs parent-child handler on an element (e.g. table) that contains
   * all the checkboxes and uses event delegation to handle individual clicks.
   * Optionally, if checkboxes are contained in table rows, clicking anywhere
   * in the row can change the state of the checkbox -- see the options object
   * for more details. 
   */
  $.fn.multicheckboxes = function(settings) {
    return this.each(function() {
      var defaults = {
        handleTableRowClicks: true,   // if true, row clicks will check checkboxes
        updateTableRowClasses: true,  // if true, css classes will be added to table rows (see below)
        selectedClass: "selected",    // style to apply to rows with selected checkboxes
        unselectedClass: "unselected",// style to apply to rows with unselected checkboxes 
        toSelectClass: "to-select",   // style to apply to rows with originally unselected checkboxes, but now selected
        toUselectClass: "to-unselect",// style to apply to rows with originally selected checkboxes, but now unselected
        selectAllSelector: "#select-all" // if provided, all checkboxes within the container will be selected/unselected according to this selector's checkbox
      };
      
      var options = $.extend({ }, defaults, settings); 

      var $container = $(this);
      $container.updatestate();
      
      $container.find(options.selectAllSelector).click(function() {
        var $checkboxes = $container.find(":checkbox").not(options.selectAllSelector);
        $checkboxes.attr("checked", this.checked);
        updateTableRow($checkboxes, options);
        $container.trigger("multichange");
      });
      
      $container.click(function(event) {
        var $target = $(event.target);
        
        // Check the element to which the event applies
        var ie6 = $.browser.msie && (parseInt($.browser.version) == 6);
        var $checkbox;
        if ($target.is(":checkbox")) {
          $checkbox = $target;
        } else if (options.handleTableRowClicks && ($target.is("td") || (ie6 && $target.is("label")))) {
          $checkbox = $target.parent().find(":checkbox");
          if (!$checkbox.get(0)) {
            return;
          }
          $checkbox.get(0).checked = !$checkbox.get(0).checked; 
        } else {
          return;
        }
        
        if ($checkbox.size() != 0 && $checkbox.attr("id")) {  
          var checkboxId = $checkbox.attr("id");
          
          $children = getChildCheckboxes($checkbox);
          if ($children.size() != 0) {
            // found children, so $parent is probably a parent
            $children.attr("checked", $checkbox.get(0).checked);
            updateTableRow($children, options);
	          updateTableRow($checkbox, options);
          }
          else {
            // didn't find children, so a child checkbox has been clicked
			      var $parent = $("#" + checkboxId.substring(0, checkboxId.indexOf("_")));
			      if ($parent.size() == 0) {
			        return;
			      }
            updateParentCheckbox($parent);			
            updateTableRow($checkbox, options);
	          updateTableRow($parent, options);
          }
          $container.trigger("multichange");
        }
      });
    });
  };
  
  $.fn.updatestate = function() {
     return this.find(":checkbox").each(function() {
       $(this).data("state", this.checked);
     });
  }; 
  
  /**
   * Returns children checkboxes for a parent checkbox. Currently, the
   * formula for parent-child relationship is hardcoded.
   */ 
  function getChildCheckboxes($parentCheckbox) {
    return $("[@id^='" + $parentCheckbox.attr("id") + "_']");
  }
  
  /**
   * Updates the state of the parent checkbox after the child checkboxes
   * have been checked/unchecked.
   */
  function updateParentCheckbox($parentCheckbox) {
    var allChecked = true;
    
    $("[@id^='" + $parentCheckbox.attr("id") + "_']").each(function() {
      if (!this.checked) {
        allChecked = false;
        return false;
      }
    });
     
    $parentCheckbox.attr("checked", allChecked);
  }
  
  /**
   * Updates the style of the table row
   */
  function updateTableRow($checkboxes, options) {
    if (options.updateTableRowClasses) {
	    $checkboxes.each(function () {
	      var $checkbox = $(this);
	      var $row = $checkbox.parents("tr").eq(0);
	      if ($row.size() == 0) {
	        return true;
	      }
	    
	      var originalState = $checkbox.data("state");
	      var newState = $checkbox.get(0).checked;
	       
		    $row.removeClass(options.selectedClass + " " + options.unselectedClass + " " + options.toSelectClass + " " + options.toUselectClass);
		    if (originalState && newState && options.selectedClass) {
		      $row.addClass(options.selectedClass);
		    }
		    else if (!originalState && !newState && options.unselectedClass) {
		      $row.addClass(options.unselectedClass);
		    }      
		    else if (originalState && !newState && options.toUselectClass) {
		      $row.addClass(options.toUselectClass);
		    }      
		    else if (!originalState && newState && options.toSelectClass) {
		      $row.addClass(options.toSelectClass);
		    }
		  });
		}
  }
})(jQuery);
