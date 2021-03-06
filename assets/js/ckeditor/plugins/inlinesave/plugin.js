CKEDITOR.plugins.add( 'inlinesave',{
	init: function( editor ){
		editor.addCommand( 'inlinesave',{
				exec : function( editor ){
					addData();
					function addData() {
						var data = editor.getData();
						var dataID = editor.container.getId();
						$.ajax({
							type: "POST",
							//Specify the name of the file you wish to use to handle the data on your public page with this code:
							//<script>var dump_file="yourfile.php";</script>
							//(Replace "yourfile.php" with the relevant file you wish to use)
							//Data can be retrieved from the variable $_POST['editabledata']
							//The ID of the editor that the data came from can be retrieved from the variable $_POST['editorID']
							url: url,
							data: { editabledata: data, editorID: dataID },
              success: function(){
                alert("Endringene er lagret.");
              },
							error: function (jqXHR, textStatus, errorThrown) {
                alert("Error saving content. [" + jqXHR.responseText + "]");
              }
						})
					}
				}
			});
		editor.ui.addButton( 'Inlinesave',{
			label: 'Save',
			command: 'inlinesave',
			icon: this.path + 'images/inlinesave.png'
		});
	}
});
