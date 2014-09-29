<html>

<script>
function unmask(truefalse) {
    for (var f in new Array('pwd', 'pwd2')) {
        oldElem = document.getElementById(f);
        elem = document.createElement('input');
        elem.setAttribute('type', (truefalse == true ? 'text' : 'password'));
        elem.setAttribute('value', document.getElementById(f).value);
        elem.id = f;
        oldElem.parentNode.replaceChild(elem, oldElem);
        };
    }
}
//end function

</script>

	<body>

<form name="myform">
   <input type="password" id="pwd2" name="pass2" />
 <input type="checkbox" onchange="document.getElementById('pwd2').type = this.checked ? 'text' : 'password'"> Reveal passwords
</form>


	</body>
	</html>