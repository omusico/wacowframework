/**
 * Dynamic Table
 *
 * Example:
 * <code>
 * </code>
 * @author Jace Ju
 * @license MIT License
 */
;(function($) { // plugin body start

var $$;

$$ = $.fn.watable = function(settings) {

    $$.container = this;

    $$.settings = jQuery.extend($$.settings, settings);

    return $$.container.each(function() {
        var table = this;

        if (!$(table).is('table')) {
            return;
        }

        // Binding event on heads of column
        var headRow = this.tHead.rows[0];
        for (var i = 0; i < headRow.cells.length; i ++) {
            $(headRow.cells[i]).click($$.settings.colHeadClick);
        }

        // Binding event on heads of row
        var tblBodyObj = this.tBodies[0];
        for (var i = 0; i < tblBodyObj.rows.length; i ++) {
            var cell = $(tblBodyObj.rows[i].cells[0]);
            if (cell.is('th')) {
                cell.click($$.settings.rowHeadClick);
            }
        }

        // Binding event on other cells
        $('td', table).click($$.settings.cellClick);

        $($$.settings.addColButtons).click(function () {
            $$.addCol.apply(table);
            return false;
        });
        $($$.settings.addRowButtons).click(function () {
            $$.addRow.apply(table);
            return false;
        });
    });

};

$$.container = null;

$$.settings = {
    minCols: 3,
    minRows: 3,
    maxCols: 11,
    maxRows: 21,
    addColButtons: '#addCol',
    addRowButtons: '#addRow',
    colHeadHtml: '&nbsp;',
    rowHeadHtml: '&nbsp;',
    cellHtml: '&nbsp;',
    colHeadClick: function () {},
    rowHeadClick: function () {},
    cellClick: function () {},
    beforeAddRow: function () {},
    beforeAddCol: function () {},
    afterAddRow: function () {},
    afterAddCol: function () {},
    beforeAlterTable: function () {},
    afterAlterTable: function () {}
};

$$.addCol = function () {
    $$.settings.beforeAlterTable.apply(this);
    $$.settings.beforeAddCol.apply(this);
    if (this.rows[0].cells.length > $$.settings.maxCols) {
        return false;
    }

    var tblHeadObj = this.tHead;
    for (var h = 0; h < tblHeadObj.rows.length; h ++) {
        var newTH = document.createElement('th');
        tblHeadObj.rows[h].appendChild(newTH);
        $(newTH).click($$.settings.colHeadClick).attr('scope', 'col').html($$.settings.colHeadHtml);
    }

    var tblBodyObj = this.tBodies[0];
    for (var i = 0; i < tblBodyObj.rows.length; i ++) {
        var newCell = tblBodyObj.rows[i].insertCell(-1);
        $(newCell).click($$.settings.cellClick).html($$.settings.cellHtml);
    }
    $$.settings.afterAddCol.apply(this);
    $$.settings.afterAlterTable.apply(this);
};

$$.addRow = function () {
    $$.settings.beforeAlterTable.apply(this);
    $$.settings.beforeAddRow.apply(this);
    if (this.rows.length > $$.settings.maxRows) {
        return false;
    }
    var lastRow = this.rows[this.rows.length - 1];
    var firstCellTagName = String(lastRow.cells[0].tagName).toUpperCase();
    var newRow = this.insertRow(this.rows.length);

    // add first cell
    var firstCell = document.createElement(firstCellTagName);
    newRow.appendChild(firstCell);

    if ('TH' == firstCellTagName) {
        $(firstCell).click($$.settings.colHeadClick).attr('scope', 'row').html($$.settings.rowHeadHtml);
    } else {
        $(firstCell).click($$.settings.cellClick).html($$.settings.cellHtml);
    }

    // add other cells
    for (var i = 1; i < lastRow.cells.length; i ++) {
        var newCell = newRow.insertCell(i);
        $(newCell).click($$.settings.cellClick).html($$.settings.cellHtml);
    }
    $$.settings.afterAddRow.apply(this);
    $$.settings.afterAlterTable.apply(this);
};

})(jQuery);