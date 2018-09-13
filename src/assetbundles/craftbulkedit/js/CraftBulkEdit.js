Craft.BulkEditPlugin = Garnish.Base.extend({

    elementActionLinks:null,
    init: function(elementType, $container, settings) {
        this.actionLinks = document.querySelectorAll('.bulkEditSubmit')
        // Loop through all element to add submit event listener
        for (var i = this.actionLinks.length - 1; i >= 0; i--) {
            this.actionLinks[i].addEventListener('click', this.submitAction.bind(this))
        }
    },

    submitAction: function(e){
        var element = e.currentTarget

        // Get javascript Craft element index object
        var elementIndex = Craft.elementIndex

        // Get all params from the view
        var viewParams = elementIndex.getViewParams()

        // Copy param from the view and add custom values for the controller
        console.log(element.dataset)
        var data = viewParams
        data.actionHandle = element.dataset.handle
        data.value = element.dataset.value
        data.elementIds = elementIndex.view.getSelectedElementIds()

        elementIndex.setIndexBusy()
        // Keep all element selected after we refreshed the list of element
        elementIndex._autoSelectElements = data.elementIds;

        // Post all params to custom bult-edit controller
        Craft.postActionRequest('craft-bulk-edit/default/perform-edit-action', data, function(response,textStatus) {
            elementIndex.setIndexAvailable();

            if (textStatus === 'success') {
                if (response.success) {
                    elementIndex._updateView(viewParams, response);

                    if (response.message) {
                        Craft.cp.displayNotice(response.message);
                    }
                }
                else {
                    Craft.cp.displayError(response.message);
                }
            }

        })
    }
})