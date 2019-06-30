/**
 * 
 */

var urlroot = $('#jsurlroot').val();

var csrfToken = $('#csrf').find('input[name="csrf_token"]').val();

$('.selectpicker').selectpicker({
	  style: 'btn-info',
	  size: 4,
	  includeSelectAllOption: true,
	});

$('#allauthorsTable').DataTable({
		 "language": {
				 "sEmptyTable": "Tabellen innehåller ingen data",
				 "sInfo": "Visar _START_ till _END_ av totalt _TOTAL_ rader",
				 "sInfoEmpty": "Visar 0 till 0 av totalt 0 rader",
				 "sInfoFiltered": "(filtrerade från totalt _MAX_ rader)",
				 "sInfoPostFix": "",
				 "sInfoThousands": " ",
				 "sLengthMenu": "Visa _MENU_ rader",
				 "sLoadingRecords": "Laddar...",
				 "sProcessing": "Bearbetar...",
				 "sSearch": "Sök:",
				 "sZeroRecords": "Hittade inga matchande resultat",
				 "oPaginate": {
					 "sFirst": "Första",
					 "sLast": "Sista",
					 "sNext": "Nästa",
					 "sPrevious": "Föregående"
				 },
				 "oAria": {
					 "sSortAscending": ": aktivera för att sortera kolumnen i stigande ordning",
					 "sSortDescending": ": aktivera för att sortera kolumnen i fallande ordning"
				 }
	        },
	  
	        "lengthMenu": [[5, 10, 25, 50, 100], [5,10, 25, 50, 100]],
	        "order": [[1, "desc"]], 
	        
	        "createdRow": function( row, data, dataIndex ) {
	        	/*$(row).addClass("d-flex");*/
	        },
	        
	        "columnDefs": [
	        { 
	        	"targets": [0], 
	        	"searchable": false, 
	        	"orderable": false, 
	        	"visible": true,
	        	"className": "w-15",
	        	"render": function(data, type, full, meta) {
	        	
	        		console.log(data);
	        		if (typeof data !== "undefined") 
	        		{
	        			
	        			if (data['ID'] >= 0)
	        			{
	        				
	        				var tmpstr = '';
	        				tmpstr += '<form method="POST" id="frmUpdateDelete">';
	        				tmpstr += '<input type="hidden" name="csrf_token" value="'+csrfToken+'">';
	        				
	        				if (data['Update'] == 1)
	        				{
	        					tmpstr += '<button type="submit" formaction="'+ urlroot + 'Authors/editauthor/'+data['ID'] +'" class="btn float-left transparent"> <i class="fa fa-user-edit text-warning fa-1x "></i></button>';
	        				}
	        				if (data['Del'] == 1)
	        				{
	        					tmpstr += '<button type="submit" formaction="'+ urlroot + 'Authors/delauthor/'+data['ID']  +'" class="btn float-right transparent delbtn"> <i class="fa fa-trash fa-1x text-danger"></i></button>';
	        				}
	        				
	        				tmpstr += '</form>';
	        				return tmpstr;
	        			}
	        		}
	        		return '';
	        		
	        		
	        
	        	}
	        	
	        	
	        },
	        
	        { 
	        	"targets": [1], 
	        	"searchable": true, 
	        	"orderable": true, 
	        	"visible": true,
	        	"className": "w-85",
	        	
	        },
	        
	        ],
	        
	        "serverSide": true,
	        "processing": true,
			"searchDelay": 600,
	        "ajax": {
	            url: 'ajaxGetAuthorData',
	            type: 'POST',
	            data:{
	            	csrf_token : csrfToken
	            }
	            
	        }
	        
	   
	 });


$('#allpublisherTable').DataTable({
	 "language": {
			 "sEmptyTable": "Tabellen innehåller ingen data",
			 "sInfo": "Visar _START_ till _END_ av totalt _TOTAL_ rader",
			 "sInfoEmpty": "Visar 0 till 0 av totalt 0 rader",
			 "sInfoFiltered": "(filtrerade från totalt _MAX_ rader)",
			 "sInfoPostFix": "",
			 "sInfoThousands": " ",
			 "sLengthMenu": "Visa _MENU_ rader",
			 "sLoadingRecords": "Laddar...",
			 "sProcessing": "Bearbetar...",
			 "sSearch": "Sök:",
			 "sZeroRecords": "Hittade inga matchande resultat",
			 "oPaginate": {
				 "sFirst": "Första",
				 "sLast": "Sista",
				 "sNext": "Nästa",
				 "sPrevious": "Föregående"
			 },
			 "oAria": {
				 "sSortAscending": ": aktivera för att sortera kolumnen i stigande ordning",
				 "sSortDescending": ": aktivera för att sortera kolumnen i fallande ordning"
			 }
       },
 
       "lengthMenu": [[5, 10, 25, 50, 100], [5,10, 25, 50, 100]],
       "order": [[1, "desc"]], 
       
       "createdRow": function( row, data, dataIndex ) {
       	/*$(row).addClass("d-flex");*/
       },
       
       "columnDefs": [
       { 
       	"targets": [0], 
       	"searchable": false, 
       	"orderable": false, 
       	"visible": true,
       	"className": "w-15",
       	"render": function(data, type, full, meta) {
       	
       		console.log(data);
       		if (typeof data !== "undefined") 
       		{
       			
       			if (data['ID'] >= 0)
       			{
       				//console.log(data['ID']);
       				var tmpstr = '';
       				tmpstr += '<form method="POST" id="frmUpdateDelete">';
       				tmpstr += '<input type="hidden" name="csrf_token" value="'+csrfToken+'">';
       				
       				if (data['Update'] == 1)
       				{
       					tmpstr += '<button type="submit" formaction="'+ urlroot + 'Publishers/editpublisher/'+data['ID'] +'" class="btn float-left transparent"> <i class="fa fa-user-edit text-warning fa-1x "></i></button>';
       				}
       				if (data['Del'] == 1)
       				{
       					tmpstr += '<button type="submit" formaction="'+ urlroot + 'Publishers/delpublisher/'+data['ID']  +'" class="btn float-right transparent delbtn"> <i class="fa fa-trash fa-1x text-danger"></i></button>';
       				}
       				tmpstr += '</form>';
       				return tmpstr;
       			}
       		}
       		return '';
       		
       		
       	}
       	
       	
       },
       
       { 
       	"targets": [1], 
       	"searchable": true, 
       	"orderable": true, 
       	"visible": true,
       	"className": "w-85",
       	
       },
       
       ],
       
       "serverSide": true,
       "processing": true,
		"searchDelay": 600,
       "ajax": {
           url: 'ajaxGetPublisherData',
           type: 'POST',
           data:{
           	csrf_token : csrfToken
           }
           
       }
       
  
});





$('#allserieTable').DataTable({
	 "language": {
			 "sEmptyTable": "Tabellen innehåller ingen data",
			 "sInfo": "Visar _START_ till _END_ av totalt _TOTAL_ rader",
			 "sInfoEmpty": "Visar 0 till 0 av totalt 0 rader",
			 "sInfoFiltered": "(filtrerade från totalt _MAX_ rader)",
			 "sInfoPostFix": "",
			 "sInfoThousands": " ",
			 "sLengthMenu": "Visa _MENU_ rader",
			 "sLoadingRecords": "Laddar...",
			 "sProcessing": "Bearbetar...",
			 "sSearch": "Sök:",
			 "sZeroRecords": "Hittade inga matchande resultat",
			 "oPaginate": {
				 "sFirst": "Första",
				 "sLast": "Sista",
				 "sNext": "Nästa",
				 "sPrevious": "Föregående"
			 },
			 "oAria": {
				 "sSortAscending": ": aktivera för att sortera kolumnen i stigande ordning",
				 "sSortDescending": ": aktivera för att sortera kolumnen i fallande ordning"
			 }
      },

      "lengthMenu": [[5, 10, 25, 50, 100], [5,10, 25, 50, 100]],
      "order": [[1, "desc"]], 
      
      "createdRow": function( row, data, dataIndex ) {
      	/*$(row).addClass("d-flex");*/
      },
      
      "columnDefs": [
      { 
      	"targets": [0], 
      	"searchable": false, 
      	"orderable": false, 
      	"visible": true,
      	"className": "w-15",
      	"render": function(data, type, full, meta) {
      	
      		
      		if (typeof data !== "undefined") 
      		{
      			
      			if (data['ID'] >= 0)
      			{
      				//console.log(data['ID']);
      				var tmpstr = '';
      				tmpstr += '<form method="POST" id="frmUpdateDelete">';
      				tmpstr += '<input type="hidden" name="csrf_token" value="'+csrfToken+'">';
      				if (data['Update'] == 1)
      				{
      					tmpstr += '<button type="submit" formaction="'+ urlroot + 'Series/editserie/'+data['ID'] +'" class="btn float-left transparent"> <i class="fa fa-user-edit text-warning fa-1x "></i></button>';
      				}
      				if (data['Del'] == 1)
      				{
      					tmpstr += '<button type="submit" formaction="'+ urlroot + 'Series/delserie/'+data['ID']  +'" class="btn float-right transparent delbtn"> <i class="fa fa-trash fa-1x text-danger"></i></button>';
      				}
      				tmpstr += '</form>';
      				return tmpstr;
      			}
      		}
      		return '';
      		
      		
      	}
      	
      	
      },
      
      { 
      	"targets": [1], 
      	"searchable": true, 
      	"orderable": true, 
      	"visible": true,
      	"className": "w-85",
      	
      },
      
      ],
      
      "serverSide": true,
      "processing": true,
		"searchDelay": 600,
      "ajax": {
          url: 'ajaxGetSerieData',
          type: 'POST',
          data:{
          	csrf_token : csrfToken
          }
          
      }
});



$('#allcategoriesTable').DataTable({
	 "language": {
			 "sEmptyTable": "Tabellen innehåller ingen data",
			 "sInfo": "Visar _START_ till _END_ av totalt _TOTAL_ rader",
			 "sInfoEmpty": "Visar 0 till 0 av totalt 0 rader",
			 "sInfoFiltered": "(filtrerade från totalt _MAX_ rader)",
			 "sInfoPostFix": "",
			 "sInfoThousands": " ",
			 "sLengthMenu": "Visa _MENU_ rader",
			 "sLoadingRecords": "Laddar...",
			 "sProcessing": "Bearbetar...",
			 "sSearch": "Sök:",
			 "sZeroRecords": "Hittade inga matchande resultat",
			 "oPaginate": {
				 "sFirst": "Första",
				 "sLast": "Sista",
				 "sNext": "Nästa",
				 "sPrevious": "Föregående"
			 },
			 "oAria": {
				 "sSortAscending": ": aktivera för att sortera kolumnen i stigande ordning",
				 "sSortDescending": ": aktivera för att sortera kolumnen i fallande ordning"
			 }
     },

     "lengthMenu": [[5, 10, 25, 50, 100], [5,10, 25, 50, 100]],
     "order": [[1, "desc"]], 
     
     "createdRow": function( row, data, dataIndex ) {
     	/*$(row).addClass("d-flex");*/
     },
     
     "columnDefs": [
     { 
     	"targets": [0], 
     	"searchable": false, 
     	"orderable": false, 
     	"visible": true,
     	"className": "w-15",
     	"render": function(data, type, full, meta) {
     	
     		
     		if (typeof data !== "undefined") 
     		{
     			
     			if (data['ID'] >= 0)
     			{
     				//console.log(data['ID']);
     				var tmpstr = '';
     				tmpstr += '<form method="POST" id="frmUpdateDelete">';
     				tmpstr += '<input type="hidden" name="csrf_token" value="'+csrfToken+'">';
     				if (data['Update'] == 1)
     				{
     					tmpstr += '<button type="submit" formaction="'+ urlroot + 'Categories/editcategory/'+data['ID'] +'" class="btn float-left transparent"> <i class="fa fa-user-edit text-warning fa-1x "></i></button>';
     				}
     				if (data['Del'] == 1)
     				{
     					tmpstr += '<button type="submit" formaction="'+ urlroot + 'Categories/delcategory/'+data['ID']  +'" class="btn float-right transparent delbtn"> <i class="fa fa-trash fa-1x text-danger"></i></button>';
     				}
     				tmpstr += '</form>';
     				return tmpstr;
     			}
     		}
     		return '';
     		
     		
     	}
     	
     	
     },
     
     { 
     	"targets": [1], 
     	"searchable": true, 
     	"orderable": true, 
     	"visible": true,
     	"className": "w-85",
     	
     },
     
     ],
     
     "serverSide": true,
     "processing": true,
		"searchDelay": 600,
     "ajax": {
         url: 'ajaxGetCategoryData',
         type: 'POST',
         data:{
         	csrf_token : csrfToken
         }
         
     }
});

$('#allbooksTable').DataTable({
	 "language": {
			 "sEmptyTable": "Tabellen innehåller ingen data",
			 "sInfo": "Visar _START_ till _END_ av totalt _TOTAL_ rader",
			 "sInfoEmpty": "Visar 0 till 0 av totalt 0 rader",
			 "sInfoFiltered": "(filtrerade från totalt _MAX_ rader)",
			 "sInfoPostFix": "",
			 "sInfoThousands": " ",
			 "sLengthMenu": "Visa _MENU_ rader",
			 "sLoadingRecords": "Laddar...",
			 "sProcessing": "Bearbetar...",
			 "sSearch": "Sök:",
			 "sZeroRecords": "Hittade inga matchande resultat",
			 "oPaginate": {
				 "sFirst": "Första",
				 "sLast": "Sista",
				 "sNext": "Nästa",
				 "sPrevious": "Föregående"
			 },
			 "oAria": {
				 "sSortAscending": ": aktivera för att sortera kolumnen i stigande ordning",
				 "sSortDescending": ": aktivera för att sortera kolumnen i fallande ordning"
			 }
      },

      "lengthMenu": [[5, 10, 25, 50, 100], [5,10, 25, 50, 100]],
      "order": [[1, "desc"]], 
      
      "createdRow": function( row, data, dataIndex ) {
      	/*$(row).addClass("d-flex");*/
      },
      
      "columnDefs": [
      { 
      	"targets": [0], 
      	"searchable": false, 
      	"orderable": false, 
      	"visible": true,
      	"className": "firstcol",
      	"render": function(data, type, full, meta) {
      	
      		
      		if (typeof data !== "undefined") 
      		{
      			if (data['ID'] >= 0)
      			{
      				//console.log(data['ID']);
      				var tmpstr = '';
      				tmpstr += '<form method="POST" id="frmUpdateDelete">';
      				tmpstr += '<input type="hidden" name="csrf_token" value="'+csrfToken+'">';
      				if (data['Update'] == 1)
      				{
      					tmpstr += '<button type="submit" formaction="'+ urlroot + 'Books/editbook/'+data['ID'] +'" class="btn float-left transparent"> <i class="fa fa-user-edit text-warning fa-1x "></i></button>';
      				}
      				if (data['Del'] == 1)
      				{
      					tmpstr += '<button type="submit" formaction="'+ urlroot + 'Books/delbook/'+data['ID']  +'" class="btn float-right transparent delbtn"> <i class="fa fa-trash fa-1x text-danger"></i></button>';
      				}
      				tmpstr += '</form>';
      				return tmpstr;
      			}
      		}
      		return '';
      		
      		
      	}
      	
      	
      },
      
      { 
      	"targets": [1,2,4,5,6,7,8],
      	"searchable": true, 
      	"orderable": true, 
      	"visible": true,
      	
      },
      { 
      	"targets": [3], 
      	"searchable": false, 
      	"orderable": false, 
      	"visible": true,
      	
      },
      { 
        	"targets": [9], 
        	"searchable": false, 
        	"orderable": true, 
        	"visible": true,
        	"render": function(data, type, full, meta) {
        	
        		
        		if (typeof data !== "undefined") 
        		{
        			
        			if (data['haveread'] == 1)
        			{

        				var tmpstr = 'Har läst';
        			
        				return tmpstr;
        			}
        			else
        			{

        				var tmpstr = 'Att läsa';
        			
        				return tmpstr;
        			}	
        			
        		}
        		return '';
        		
        		
        	}
        	
        	
        },
      
      
      
      ],
      
      "serverSide": true,
      "processing": true,
		"searchDelay": 600,
      "ajax": {
          url: 'ajaxGetBooksData',
          type: 'POST',
          data:{
          	csrf_token : csrfToken
          }
          
      }
      
 
});


