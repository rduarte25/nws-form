function eliminar(id) {
	Swal.fire({
		title: '¿Estas seguro?',
		text: "¡Esta acción no se puede revertir!",
		icon: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: 'Sí, eliminar!',
		cancelButtonText: 'Cancelar'
	  }).then((result) => {
		if (result.value) {
		  jQuery.ajax( {
			  type: 'post',
			  data: {
				  'action' : 'nws_form_eliminar',
				  'id': id,
				  'tipo': 'eliminar'
			  },
			  url: url.ajaxurl,
			  success: function( data ) {
				  var resultado = JSON.parse( data );
				  //console.log( resultado );
				  if ( resultado.respuesta == 1 ) {
					  jQuery( "[data-data_user='"+ resultado.id +"']" ).parent().parent().remove();
					  
					  Swal.fire(
							'¡Eliminado!',
							'¡El mesaje se ha eliminado!',
							'success'
					  )
					  window.location.href= "?page=nws_form_registros";
				  } else {
					  Swal.fire(
						  '¡Error!',
						  '¡Ocurrio un error!',
						  'error'
					)
				  }
			  }
		  } );
		}
	});
}


jQuery( '#borrar_registro' ).on( 'click', function(e){
	e.preventDefault();
	console.log('click')

	var id = jQuery( this ).attr( 'data-data_user' );
	
} );

function init() {
	listar(jQuery('#tblregistro'),url.ajaxurl);
}

//Función listar.
function listar(tablaC, url) {
	tabla = jQuery('#tblregistro').dataTable( {
	  "language": {
			"decimal": "",
			"emptyTable": "No hay información",
			"info": "Mostrando _START_ a _END_ de _TOTAL_ Entradas",
			"infoEmpty": "Mostrando 0 to 0 of 0 Entradas",
			"infoFiltered": "(Filtrado de _MAX_ total entradas)",
			"infoPostFix": "",
			"thousands": ",",
			"lengthMenu": "Mostrar _MENU_ Entradas",
			"loadingRecords": "Cargando...",
			"processing": "Procesando...",
			"search": "Buscar:",
			"zeroRecords": "Sin resultados encontrados",
			"paginate": {
				"first": "Primero",
				"last": "Ultimo",
				"next": "Siguiente",
				"previous": "Anterior"
			}
		},
	  "aProcessing":true,
	  "aServerSide":true,
	  "destroy":true,
	  dom: "Blfrtipo",
	  buttons: [
			'copyHtml5',
			'excelHtml5',
			'csvHtml5',
			'pdfHtml5',
			'print',
		  ],
	  "ajax": {
		  "url": url,
		  "type":"POST",
		  "data": {
			'action' : 'nws_form_get_registros',},
		  "dataType": "JSON",
		  error: function( e ) {
			console.log( e.responseText );
		  },
		},
	  "bDistory":true,
	  "iDisplayLength":10,
	  "order": [[ 0,"desc" ]],
	  initComplete: function () {
		this.api().columns().every(function () {
		  var column = this;
		  var select = jQuery('<select><option value=""></option></select>')
			.appendTo( jQuery(column.footer() ).empty() )
			.on ( 'change', function() {
			  var val = jQuery.fn.dataTable.util.escapeRegex(
				jQuery( this ).val()
			  );

			  column
				.search( val ? '^'+val+'jQuery': '', true, false )
				.draw();
			} );
		  column.data().unique().sort().each( function ( d,j ) {
			select.append( '<option value="'+d+'">'+d+'</option>' )
		  } );
		  tabla.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
			cell.innerHTML = i+1;
		} );
		});
	  }
	} ).DataTable();
  }

init();