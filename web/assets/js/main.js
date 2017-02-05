$( document ).ready(function() {


	$(document).on("keyup paste", '#create-project-field-projectname', function() {
		$("#create-project-field-defaultdb").val($(this).val());
	});

	$(document).on('click', '#advanced-settings-title', function(){
		$('#advanced-settings').slideToggle('slow');
	});


// $.get("/repos", function(html) {
// 	console.log(html);
// });
// $.when(
//     $.ajax("/some/page"),
//     $.ajax("/some/other-page"),
//     $.ajax("/some/other-other-page"),
//     $.ajax("/and/so/on")
//     )
//     .done(function(first_call, second_call, third_call, fourth_call){
//        console.log(first_call);
//     })
//     .fail(function(){
//         //ruh roh - bail out!
//     });


// $('#btn-new-project').click(function(event) {
//   event.preventDefault();
//   $.get(this.href, function(html) {
//     $(html).appendTo('body').modal();
//   });
// });



// $.get("/feature/", function(html) {

//   // Get the CSS
//   $.get("/assets/feature.css", function(css) {

//     // Get the JavaScript
//     $.getScript("/assets/feature.js", function() {

//        // All is ready now, so...

//        // Add CSS to page
//        $("<style />").html(css).appendTo("head");

//        // Add HTML to page
//        $("body").append(html);

//     });

//   });

// });


// $.get("/repos/", function(html) {
// console.log(html);

// });


// Project list
$('#btn-new-project').magnificPopup({
	type: 'ajax',
	closeBtnInside: false,
	mainClass: 'mfp-3d-unfold',
	  removalDelay: 500, //delay removal by X to allow out-animation
	  callbacks: {
	  	ajaxContentAdded: function() {
	  	}
	  },
	});



$(document).on('click', '#btn-create-project', function(e){
	e.preventDefault();
	// Get data from input fields
	var formData = $('#form-repo-new form').serializeArray();
	// Launch repo set up
	repoSetup(formData);
});


function repoSetup(formData) {
	// $.magnificPopup.open({
	// 	items: {
	// 		src: '<div id="modal-repo-setup"></div>',
	// 		type: 'inline'
	// 	}
	// });

	$.get("https://temporator.timmxware.fr/repos/create", formData, function(projectId) {

		formData.push({"name":"projectid", "value":projectId});

		console.log(formData);
		$.get("/repos/commit", formData, function(status) {
		  location.reload();
		});
	// $('#modal-repo-setup').append(html);
});


}


// $('#btn-new-project').on($.modal.BEFORE_CLOSE, function(event, modal) {
//   console.log("hey");
// });







	// $('#btn-create-project').on('click', function(e){
	// 	e.preventDefault();
	// 	var client = new $.RestClient(apiUrl+'/');
	// 	client.add('projects');
	// 	client.projects.create({ access_token:token, name: 'toto1' }).done(function (data){
	// 	// for (i in data)
	// 	// {
	// 	// 	console.log(data[i].name);

	// 	// }
	// 	console.log(data);

	// });
	// });




});


