// © Licensed Authorship: Manuel J. Nieves (See LICENSE for terms)
/* 
 * Auxiliarry utilities to work with the jstree
 */

/**
 * Visit each node in the tree with a callback function provided.
 * @param strJsTreeSelector This can be a DOM node, jQuery node or selector pointing to the tree container, or an element within the tree.
 * @param callback The callback function to be called on each node. It has a form of function(node){}
 * @param node The node starts with. If it is provided, function would traverse only it's children's tree.
 */
function visit_all_nodes(strJsTreeSelector, callback, node) {
	if (undefined === strJsTreeSelector) {
		return;
	}
	if (undefined === callback) {
		return;
	}
	if (undefined === node) {
		node = -1;
	}
	jQuery.jstree._reference(strJsTreeSelector)._get_children(node).each(function () {
		callback(this);
		visit_all_nodes(strJsTreeSelector, callback, this)
	});
}