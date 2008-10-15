<?
//por bug en el framework
//	'checkbox' => '<input name="data[%s][%s]" %s/>',

$tags = array(
	"radio" => "<p class='radio'><input type='radio' name='%s' id='%s' %s /><label for='%2\$s' class='radio_label'>%s</label></p>",
	"checkboxmultiple" => "<input type='checkbox' class='checkbox' name='data[%s][%s][]' %s/>",
	"hiddenmultiple" => "<input type='hidden' name='data[%s][%s][]' />%s",
	"dt" => "<dt %s />%s</dt>",
	"dd" => "<dd/>%s</dd>",
	"dl" => "<dl/>%s</dl>",
	"legend" => "<legend><span>%s</span></legend>",
	"table" => "<table>%s</table>",
	"tbody" => "<tbody>%s</tbody>",
	"thead" => "<thead>%s</thead>",
	"tfoot" => "<tfoot>%s</tfoot>"
);

?>