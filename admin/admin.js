jQuery(document).ready(function ($) {
    var templatesContainer = $('#cf7-chatgpt-templates');



    // Render the list of templates
    function renderTemplatesList(templates) {
        var list = $('<ul class="cf7-chatgpt-templates-list"></ul>');

        templates.forEach(function (template) {
            var listItem = $('<li></li>');
            listItem.append('<span>' + template.name + '</span>');
            listItem.append('<button class="edit-template" data-id="' + template.id + '">Edit</button>');
            listItem.append('<button class="delete-template" data-id="' + template.id + '">Delete</button>');
            list.append(listItem);
        });

        templatesContainer.html(list);
    }

    // Add event listeners for edit and delete buttons
    templatesContainer.on('click', '.edit-template', function () {
        var templateId = $(this).data('id');
        console.log('Edit template: ', templateId);

        // Load the template data and show the edit form.
    });

    templatesContainer.on('click', '.delete-template', function () {
        var templateId = $(this).data('id');
        console.log('Delete template: ', templateId);

        // Confirm the deletion and remove the template.
    });
// Render the conditional logic rules
function renderConditionalLogicRules(rules) {
    var rulesContainer = $('#cf7-chatgpt-conditional-logic-rules');
    var list = $('<ul class="cf7-chatgpt-rules-list"></ul>');

    rules.forEach(function (rule) {
        var listItem = $('<li></li>');
        listItem.append('<span>IF ' + rule.field + ' ' + rule.operator + ' "' + rule.value + '", USE TEMPLATE ' + rule.template_id + '</span>');
        listItem.append('<button class="delete-rule" data-id="' + rule.id + '">Delete</button>');
        list.append(listItem);
    });

    rulesContainer.html(list);
}

// Handle adding a new rule
$('#cf7-chatgpt-add-rule').on('click', function () {
    // Show a form to add a new rule, and then update the rules list.
});

// Handle deleting a rule
$('#cf7-chatgpt-conditional-logic-rules').on('click', '.delete-rule', function () {
    var ruleId = $(this).data('id');
    console.log('Delete rule: ', ruleId);

    // Confirm the deletion and remove the rule.
});
function fetchTemplates() {
    $.ajax({
        url: 'path/to/your/server/endpoint',
        method: 'GET',
        success: function (response) {
            renderTemplatesList(response.templates);
        },
        error: function (error) {
            console.error('Error fetching templates:', error);
        }
    });
}
function fetchRules() {
    $.ajax({
        url: 'path/to/your/server/endpoint',
        method: 'GET',
        success: function (response) {
            renderConditionalLogicRules(response.rules);
        },
        error: function (error) {
            console.error('Error fetching rules:', error);
        }
    });
}

fetchRules();
function addTemplate(templateData) {
    $.ajax({
        url: 'path/to/your/server/endpoint',
        method: 'POST',
        data: templateData,
        success: function (response) {
            fetchTemplates(); // Refresh the templates list
        },
        error: function (error) {
            console.error('Error adding template:', error);
        }
    });
}
function editTemplate(templateId, templateData) {
    $.ajax({
        url: 'path/to/your/server/endpoint/' + templateId,
        method: 'PUT',
        data: templateData,
        success: function (response) {
            fetchTemplates(); // Refresh the templates list
        },
        error: function (error) {
            console.error('Error editing template:', error);
        }
    });
}

templatesContainer.on('click', '.delete-template', function () {
    var templateId = $(this).data('id');
    if (confirm('Are you sure you want to delete this template?')) {
        $.ajax({
          url: 'path/to/your/server/endpoint/' + templateId,

            method: 'DELETE',
            success: function (response) {
                fetchTemplates(); // Refresh the templates list
            },
            error: function (error) {
                console.error('Error deleting template:', error);
            }
        });
    }
});

// Fetch and render the rules when the page loads
// Replace the following line with an AJAX request to fetch rules from the server.


renderConditionalLogicRules(rules);

    // Fetch and render the templates when the page loads
    // Fetch and render the templates and rules when the page loads
fetchTemplates();
fetchRules();
});
