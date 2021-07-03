let a = 0;
let b = 0;
$(document).ready(function () {

    /* BUILD DATATABLES */
    let table = $('#datatable').DataTable({
        responsive: true,
        // All columns    
        columns: [
            {data: 'Nom et prénom'},
            {data: 'Pseudonyme'},
            {data: 'Email'},
            {data: 'Verification email'},
            {data: 'Actions'}
        ],

        // Column Actions 
        columnDefs: [
        {
            targets: 4,
            data: "Actions",
            searchable: false,
            orderable: false
        }],
        

        language: {
            "sEmptyTable": "Aucune donnée disponible dans le tableau",
            "sInfo": "Affichage de l'élément _START_ à _END_ sur _TOTAL_ éléments",
            "sInfoEmpty": "Affichage de l'élément 0 à 0 sur 0 élément",
            "sInfoFiltered": "(filtré à partir de _MAX_ éléments au total)",
            "sInfoThousands": ",",
            "sLengthMenu": "Afficher _MENU_ éléments",
            "sLoadingRecords": "Chargement...",
            "sProcessing": "Traitement...",
            "sSearch": "",
            "sZeroRecords": "Aucun élément correspondant trouvé",
            "oPaginate": {
                "sFirst": "Premier",
                "sLast": "Dernier",
                "sNext": "Suivant",
                "sPrevious": "Précédent"
            },
            "oAria": {
                "sSortAscending": ": activer pour trier la colonne par ordre croissant",
                "sSortDescending": ": activer pour trier la colonne par ordre décroissant"
            },
            "select": {
                "rows": {
                    "_": "%d lignes sélectionnées",
                    "0": "Aucune ligne sélectionnée",
                    "1": "1 ligne sélectionnée"
                }
            }
        },
    });

          
    // On page load, display user
    getUsersByRole('user');
    getUsersdeletedAt('user');

    // Display different types on filtering button click
    $(".filtering-btn").click(function() {
        $(".filtering-btn").removeClass('active');
        $(this).addClass('active');
        table.columns( [0] ).visible( true );
        if(this.id === "user") {
            table.columns( [0] ).visible( false );
        }
        getUsersByRole(this.id);
    });

    // Display last filtering column to show deleted user
    $(".filter-delete").click(function() {
        $(".filter-delete").removeClass('active');
        $(this).addClass('active');
        table.columns( [0] ).visible( true );
        if(this.id === "user") {
            table.columns( [0] ).visible( false );
        }
        getUsersByRole(this.id);
    });

    function getUsersByRole(role) {
        $.ajax({
            type: 'POST',
            url: callRoute('users_data'),
            data: { role },
            dataType: 'json',
            success: function(response) {
                table.clear();
                table.rows.add(response.users).draw();
            },
            error: function(){
                console.log("Erreur dans la récupération des utilisateurs de role " + role);
            }
        });
    }

    //getUser if deletedAt
    function getUsersdeletedAt(deletedAt) {
        $.ajax({
            type: 'POST',
            url: callRoute('users_data'),
            data: { deletedAt },
            dataType: 'json',
            success: function(response) {
                table.clear();
                table.rows.add(response.users).draw();
            },
            error: function(){
                console.log("Erreur dans la récupération des utilisateurs de deletedAt " + deletedAt);
            }
        });
    }

    /* Delete User*/
    table.on('click', '.delete', function(event) {
        event.preventDefault();
        if (confirm('Êtes-vous sûr de vouloir supprimer cette utilisateur ?')) {
            let personId = this.id.substring(this.id.lastIndexOf('-') + 1);
            let row = table.row($(this).parents('tr'));
            $.ajax({
                type: 'POST',
                url: callRoute("users_delete"),
                data: { id: personId },
                success: function() {
                    row.remove().draw();
                },
                error: function() {
                    $('.header').after("Erreur dans la suppression de l'utilisateur ID " + personId);
                }
            });
        }
    });

});