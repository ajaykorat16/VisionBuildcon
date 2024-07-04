var Delete = Delete || {};

(function($, module) {
    module.onShowModal = function () {
        $(function() {
            const $modal = $('#deleteModal');
            const $deleteForm = $('#deleteButton');
        
            $('.delete').on('click', function() {
                const { name, id, entity } = $(this).data();
        
                $('#deleteName').text(name);
                $('#deleteId').val(id);
                $('#deleteEntity').val(entity);
        
                $deleteForm.attr('href', `/admin/${entity}/delete/${id}`);
                $modal.modal('show');
            });
        });
    }
})(jQuery, Delete);