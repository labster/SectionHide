function toggleSectionVisibility(fieldObj, id, txt1, txt2)
{
	var e = document.getElementById('sectionblock'+id);
	if(txt1.search(/gif:/i) >= 0) txt1 = '<img src="'+txt1.replace(':', '">');
	if(txt2.search(/gif:/i) >= 0) txt2 = '<img src="'+txt2.replace(':', '">');
	if(fieldObj.innerHTML == txt1)
	{
		fieldObj.innerHTML = txt2;
		e.style.display = 'block';
	}
	else
	{
		fieldObj.innerHTML = txt1;
		e.style.display = 'none';
	}
}
