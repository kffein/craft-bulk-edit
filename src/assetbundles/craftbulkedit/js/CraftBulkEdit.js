Craft.BulkEditPlugin = Garnish.Base.extend({
    elementActionLinks:null,
    init: function(elementType, $container, settings) {
        this.actionLinks = document.querySelectorAll('.bulkEditSubmit')
        for (var i = this.actionLinks.length - 1; i >= 0; i--) {
            this.actionLinks[i].addEventListener('click', this.submitAction.bind(this))
        }
    },

    submitAction: function(e){
        var element = e.currentTarget
        
        var elementIndex = Craft.elementIndex
        var viewParams = elementIndex.getViewParams()
        data = viewParams
        data.actionHandle = element.dataset.handle
        data.value = element.dataset.value
        data.elementIds = elementIndex.view.getSelectedElementIds()

        Craft.postActionRequest('craft-bulk-edit/default/perform-edit-action', data, function(response,textStatus) {
            elementIndex.setIndexAvailable();

            if (textStatus === 'success') {
                if (response.success) {
                    elementIndex._updateView(viewParams, response);

                    if (response.message) {
                        Craft.cp.displayNotice(response.message);
                    }

                    elementIndex.afterAction(action, params);
                }
                else {
                    Craft.cp.displayError(response.message);
                }
            }
        })
    }
})