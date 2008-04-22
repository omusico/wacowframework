/**
 * Get document object of iframe
 *
 * @param object iframeObject
 */
jQuery.getIframeDocument = function (iframeObject) {
    if(iframeObject.contentWindow) {
        return iframeObject.contentWindow.document;
    } else if (iframeObject.contentDocument) {
        return iframeObject.contentDocument.document;
    }
    return null;
};