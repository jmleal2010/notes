import $ from 'jquery'
import axios from "axios";
import Swal from 'sweetalert2'

$(function () {
    initComponents();
});

const initComponents = () => {
    initDatatables();
    initFunctions();
}

const initDatatables = () => {
    $('#note-table').DataTable({
        paging: false,
        ordering: true,
        oLanguage: {
            "sLengthMenu": "Mostrar _MENU_ registros por página",
            "sZeroRecords": "Nothing found - sorry",
            "sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando 0 a 0 of de registros",
            "sInfoFiltered": "(filtrado de _MAX_ total de registros)",
            "sSearch": "Buscar:"
        }
    });
    $('input[type=search]').addClass('form-control')
}

const initSelect2 = async () => {
    const result = await axios.get(Routing.generate('get_tags'))
    const tagSelect = $('#note_tags')

    try {
        const tags = JSON.parse(tagSelect.attr('data-elements')).map(item => item.title)

        tagSelect
            .select2({
                closeOnSelect: true,
                tags: true,
                data: [result.data],
                width: '100%',
            })
            .select2('val', [tags])
    } catch (err) {
        console.log(err.message)
    }
}
const initSwal = () => {
    const btnDelete = $('.note-delete')
    const btnRestore = $('.restore-note')

    btnDelete.on('click', () => {
        Swal.fire({
            title: 'Seguro que desea eliminar el elemento?',
            text: "Puede revertirlo luego si lo desea!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si, eliminarlo!'
        })
            .then(async (result) => {
                if (result.isConfirmed) {
                    const bodyFormData = new FormData();

                    bodyFormData.append('_token', btnDelete.attr('data-token'));

                    const result = await axios({
                        method: 'POST',
                        url: Routing.generate('app_note_delete', {id: btnDelete.attr('id')}),
                        data: bodyFormData
                    })

                    Swal.fire({
                        title: 'Eliminado!',
                        text: 'El elemento ha sido eliminado.',
                        icon: 'success'
                    }).then(async (result) => {
                        console.log(result)
                        if (result.isConfirmed) {
                            window.open("/note", '_self');
                        }
                    })
                }
            })
    })
    btnRestore.on('click', () => {
        Swal.fire({
            title: 'Seguro que desea restaurar el elemento?',
            text: "Puede volverlo a eliminar si lo desea!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si, restaurarlo!'
        })
            .then(async (result) => {
                if (result.isConfirmed) {
                    const bodyFormData = new FormData();

                    bodyFormData.append('_token', btnRestore.attr('data-token'));
                    const result = await axios({
                        method: 'POST',
                        url: Routing.generate('app_restore_note', {id: btnRestore.attr('id')}),
                        data: bodyFormData
                    })

                    Swal.fire({
                        title: 'Restaurado!',
                        text: 'El elemento ha sido restaurado.',
                        icon: 'success'
                    }).then(async (result) => {
                        if (result.isConfirmed) {
                            window.open("/note", '_self');
                        }
                    })
                }
            })
    })
}
const initFunctions = () => {
    initSelect2();
    initSwal();
}