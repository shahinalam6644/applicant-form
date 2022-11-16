//table search
jQuery(document).ready(function(){
  jQuery("#afInputsearch").on("keyup", function() {
    var value = jQuery(this).val().toLowerCase();
    jQuery("#afmyTable tr").filter(function() {
      jQuery(this).toggle(jQuery(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
});

//shoting date---------
function sortTable(e) {
  var th = e.target;
  if (th.nodeName.toLowerCase() !== 'th') return true;

  var n = 0; while (th.parentNode.cells[n] != th) ++n;
  var order = th.order || 1; th.order = -order;
  var t = this.querySelector('tbody');

  t.innerHTML = Object.keys(t.rows)
    .filter(k => !isNaN(k))
    .map(k => t.rows[k])
    .sort((a, b) => order * (typed(a) > typed(b) ? 1 : -1))
    .map(r => r.outerHTML)
    .join('');

  function typed(tr) {
    var s = tr.cells[n].innerText;
    var d = Date.parse(s.replace(/^(\d{1,2})st|th/, '$1'))
    return isNaN(d) ? s.toLowerCase() : d;
  }
}

document.querySelector('#paleBlueRows')
  .addEventListener('click', sortTable, false);
  
// paginaion----------------------------------------
getPagination('#paleBlueRows');		 

function getPagination(table) {
  var lastPage = 1;

  jQuery('#maxRows')
    .on('change', function(evt) {
      //jQuery('.paginationprev').html('');						// reset pagination

     lastPage = 1;
      jQuery('.pagination')
        .find('li')
        .slice(1, -1)
        .remove();
      var trnum = 0; // reset tr counter
      var maxRows = parseInt(jQuery(this).val()); // get Max Rows from select option

      if (maxRows == 5000) {
        jQuery('.pagination').hide();
      } else {
        jQuery('.pagination').show();
      }

      var totalRows = jQuery(table + ' tbody tr').length; // numbers of rows
      jQuery(table + ' tr:gt(0)').each(function() {
        // each TR in  table and not the header
        trnum++; // Start Counter
        if (trnum > maxRows) {
          // if tr number gt maxRows

          jQuery(this).hide(); // fade it out
        }
        if (trnum <= maxRows) {
          jQuery(this).show();
        } // else fade in Important in case if it ..
      }); //  was fade out to fade it in
      if (totalRows > maxRows) {
        // if tr total rows gt max rows option
        var pagenum = Math.ceil(totalRows / maxRows); // ceil total(rows/maxrows) to get ..
        //	numbers of pages
        for (var i = 1; i <= pagenum; ) {
          // for each page append pagination li
          jQuery('.pagination #prev')
            .before(
              '<li data-page="' +
                i +
                '">\
								  <span>' +
                i++ +
                '<span class="sr-only">(current)</span></span>\
								</li>'
            )
            .show();
        } // end for i
      } // end if row count > max rows
      jQuery('.pagination [data-page="1"]').addClass('active'); // add active class to the first li
      jQuery('.pagination li').on('click', function(evt) {
        // on click each page
        evt.stopImmediatePropagation();
        evt.preventDefault();
        var pageNum = jQuery(this).attr('data-page'); // get it's number

        var maxRows = parseInt(jQuery('#maxRows').val()); // get Max Rows from select option

        if (pageNum == 'prev') {
          if (lastPage == 1) {
            return;
          }
          pageNum = --lastPage;
        }
        if (pageNum == 'next') {
          if (lastPage == jQuery('.pagination li').length - 2) {
            return;
          }
          pageNum = ++lastPage;
        }

        lastPage = pageNum;
        var trIndex = 0; // reset tr counter
        jQuery('.pagination li').removeClass('active'); // remove active class from all li
        jQuery('.pagination [data-page="' + lastPage + '"]').addClass('active'); // add active class to the clicked
        // jQuery(this).addClass('active');					// add active class to the clicked
	  	limitPagging();
        jQuery(table + ' tr:gt(0)').each(function() {
          // each tr in table not the header
          trIndex++; // tr index counter
          // if tr index gt maxRows*pageNum or lt maxRows*pageNum-maxRows fade if out
          if (
            trIndex > maxRows * pageNum ||
            trIndex <= maxRows * pageNum - maxRows
          ) {
            jQuery(this).hide();
          } else {
            jQuery(this).show();
          } //else fade in
        }); // end of for each tr in table
      }); // end of on click pagination list
	  limitPagging();
    })
    .val(5)
    .change();

  // end of on select change
  // END OF PAGINATION
}

function limitPagging(){
	// alert(jQuery('.pagination li').length)

	if(jQuery('.pagination li').length > 7 ){
			if( jQuery('.pagination li.active').attr('data-page') <= 3 ){
			jQuery('.pagination li:gt(5)').hide();
			jQuery('.pagination li:lt(5)').show();
			jQuery('.pagination [data-page="next"]').show();
		}if (jQuery('.pagination li.active').attr('data-page') > 3){
			jQuery('.pagination li:gt(0)').hide();
			jQuery('.pagination [data-page="next"]').show();
			for( let i = ( parseInt(jQuery('.pagination li.active').attr('data-page'))  -2 )  ; i <= ( parseInt(jQuery('.pagination li.active').attr('data-page'))  + 2 ) ; i++ ){
				jQuery('.pagination [data-page="'+i+'"]').show();

			}

		}
	}
}

// jQuery(function() {
//   // Just to append id number for each row
//   jQuery('table tr:eq(0)').prepend('<th> ID </th>');

//   var id = 0;

//   jQuery('table tr:gt(0)').each(function() {
//     id++;
//     jQuery(this).prepend('<td>' + id + '</td>');
//   });
// });

// Tooltip-----
function afTooltipFunction() {
  var copyText = document.getElementById("afInput");
  copyText.select();
  copyText.setSelectionRange(0, 99999);
  navigator.clipboard.writeText(copyText.value);
  
  var tooltip = document.getElementById("afTooltip");
  tooltip.innerHTML = "Copied: " + copyText.value;
}

function afoutFunc() {
  var tooltip = document.getElementById("afTooltip");
  tooltip.innerHTML = "Copy to clipboard";
}