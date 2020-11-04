<div class="wrap" style="max-width: 700px;">

    <h1>How to use FAQs</h1>
    <p>FAQs can be displayed anywhere on your website by using shortcodes.</p>
    <p>You can either display categories of FAQs related to certain topics (a "Sales" category to display on an eCommerce page for example), or display all of your FAQs at once.</p>
    <p><strong>Note:</strong> shortcodes must be used in the content editor of pages and posts.</p>

    <br><hr><br>

    <h3>Displaying a category of FAQs</h3>
    <p>If you plan to use FAQs to cover multiple topics, or want to be able to control which questions appear in different areas of your website, then you'll need to use categories.</p>
    <p>First, you'll need decide which categories you think you'll need.</p>
    <p>Go to the <a href="/wp-admin/edit-tags.php?taxonomy=<?php echo ORGNK_FAQS_CATEGORIES_TAX_NAME ?>&post_type=<?php echo ORGNK_FAQS_CPT_NAME ?>">Edit FAQ Categories</a> page and add your categories. When adding new categories, you only need to enter a 'Name' for each and then click the 'Add New Category' button. Once created, take note of the slug for the category in the category table.</p>
    <p>Now, <a href="/wp-admin/post-new.php?post_type=<?php echo ORGNK_FAQS_CPT_NAME ?>">add some FAQs</a>, and assign these to one or more of your categories.</p>

    <p>When you've added some FAQs, you can now display them on any page on your website. Find the page, click 'Edit' and place the following shortcode where you would like the FAQs to appear.</p>
    <code style="display: block; font-family: monospace; margin: 0 0 1em 0; font-size: 16px; background-color: #fff; padding: 10px; border-radius: 4px;">[<?php echo ORGNK_FAQS_SHORTCODE_NAME ?> category='category-slug']</code>
    <p><strong>Important:</strong> Replace 'category-slug' with the slug of the category you'd like to use.</p>

    <br><hr><br>

    <h3>Displaying all FAQs</h3>
    <p>If you only want a single FAQs page, with all of your FAQs displayed, then you don't need to use categories, and you can simply use the following shortcode to display all FAQs.</p>
    <code style="display: block; font-family: monospace; margin: 0 0 1em 0; font-size: 16px; background-color: #fff; padding: 10px; border-radius: 4px;">[<?php echo ORGNK_FAQS_SHORTCODE_NAME ?>]</code></p>
    <p><strong>Note:</strong> This shortcode will always display all FAQs, even if you are using categories.</p>

    <br><hr><br>

    <h3>FAQs display style</h3>
    <p>By default, FAQs are displayed in an accordion that the user can expand and collapse. If you would like to display the FAQs like normal page content, you can set the 'style' parameter to 'list'.</p>
    <code style="display: block; font-family: monospace; margin: 0 0 1em 0; font-size: 16px; background-color: #fff; padding: 10px; border-radius: 4px;">[<?php echo ORGNK_FAQS_SHORTCODE_NAME ?> style="list"]</code></p>
    <p><strong>Note:</strong> The 'style' parameter is only applied to each instance of an FAQ shortcode.</p>
</div>