<?php if ( $faqs_loop->have_posts() ) : ?>

    <div id="orgnk-faqs-<?php echo $instance ?>" class="orgnk-faqs type-<?php echo $display_type ?>">
        <div class="faqs-list">

            <?php
            while ( $faqs_loop->have_posts() ) : $faqs_loop->the_post();

                // Only display the FAQ item if it has an answer (content) set
                if ( get_the_content() ) :

                    if ( $display_type === 'list' && $faqs_loop->current_post > 0 ) echo '<hr>' ?>

                    <div <?php post_class( 'entry faq-item' ) ?>>

                        <?php if ( $display_type === 'list' ) : ?>

                            <<?php echo $heading_level ?> class="question"><?php echo esc_html( the_title() ) ?></<?php echo $heading_level ?>>

                            <div class="answer">
                                <?php echo wpautop( wp_kses_post( get_the_content() ) ) ?>
                            </div>

                        <?php else : ?>

                            <button id="faq-<?php the_ID() ?>" class="question" aria-controls="faq-<?php the_ID() ?>-answer" aria-expanded="false">
                                <div class="button-inner">
                                    <?php echo esc_html( the_title() ) ?>
                                    <i class="icon" aria-hidden="true"></i>
                                </div>
                            </button>

                            <div id="faq-<?php the_ID() ?>-answer" class="answer" aria-labelledby="faq-<?php the_ID() ?>" role="region">
                                <?php echo wpautop( wp_kses_post( get_the_content() ) ) ?>
                            </div>

                        <?php endif ?>
                    </div>
                <?php endif;
            endwhile; wp_reset_postdata() ?>
        </div>
    </div>

<?php endif;
