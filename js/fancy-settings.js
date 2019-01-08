document.addEventListener('DOMContentLoaded', function(){
    // do something


function addItem () {
    //find out how long the list is
  let num = document.getElementById('list-wrap'); 
   num = num.children;
    num = num.length;
    num += 1;
   let newP = document.createElement('p');
    let newInput = document.createElement('input');
    newInput.id = 'item-' + num;
    newInput.setAttribute('value', 'new list item');
    newInput.style.cssText = "font-size:1.5em;";
    let newLabel = document.createElement('label');
  let textString = '#' + num + '  Item';
    let labelText = document.createTextNode(textString);
    let removeButton = document.createElement('button');
    removeButton.id = num;
    removeButton.className = "remover"
    let removeButtonText = document.createTextNode('X');
    removeButton.appendChild(removeButtonText);
    newP.appendChild(newInput);
    newP.appendChild(newLabel);
    newP.appendChild(removeButton);
    newLabel.appendChild(labelText);
    document.getElementById('list-wrap').appendChild(newP);
    document.getElementById(num).addEventListener('click', removeItem);
  }
  function removeItem (e){
   function adjustNumbering(){
     let listo = document.getElementById('list-wrap');
    let kidos = listo.children;
    if(kidos.length > 2){
      
      for(let i=1; i < kidos.length; i++){
        let adjust = i + 1;
      let thingsToChange = kidos[i].children;
       thingsToChange[0].id = "item-" + adjust;
        thingsToChange[1].innerHTML = "#" + adjust + " Item";
    }
    }
   }
    e.srcElement.parentElement.remove();
    adjustNumbering();
  }
  function saveList(){
    let above = document.getElementById('aboveText');
    let output = [];
    output.push(above.value);
    let list = document.getElementById('list-wrap');
    let kids = list.children;
    for(let i=0;i<kids.length;i++){
    //  console.log(kids[i].firstChild.value)
      let targ = kids[i].firstChild.value;
      output.push(targ);
    }
    console.log(output);
    jQuery(document).ready(function($) {

      var data = {
        'action': 'my_action',
        'whatever': output
      };
  
      // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
      jQuery.post(ajaxurl, data, function(response) {
        alert(response);
      });
    });
  
  }
  
  document.getElementById('addItemButton').addEventListener('click', addItem);
  let update = document.getElementsByClassName('remover');
  for(let element of update){
    element.addEventLisener('click', removeItem);
  }
  let saveButton = document.getElementById('saveChanges');
  saveButton.addEventListener('click', saveList);
});