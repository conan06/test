// 导师评价、专业评价

var forms = document.getElementById("maindiv");
var tds = forms.getElementsByClassName("div_table_radio_question");
var radios = [];

for (var i = 0; i < tds.length; i++) {
  if (tds[i].getElementsByClassName("tdclass").length > 0)
    radios.push(tds[i]);
}

radios.forEach( elm => elm.getElementsByTagName("input")[9].checked = true; );
forms.getElementsByTagName("textarea")[0].value="GOOD!";
document.getElementById("submit_button").click();


// 课程评分

var forms = document.getElementsByClassName("toptable");
var tds = forms[1].getElementsByTagName("td");
var radios = [];
var textbox = document.getElementById("txt_remark");

for (var i = 0; i < tds.length; i++) {
  if (tds[i].getElementsByClassName("spanW").length > 0)
    radios.push(tds[i]);
}

radios.forEach( elm => elm.getElementsByClassName("spanW")[9].getElementsByTagName("input")[0].checked = true; );
textbox.innerHTML="GOOD!";
document.getElementById("Submit1").click();
