<?php
/**
 * Template to render RePack Statistics.
 *
 * @package   RePack
 * @since     1.0.0
 * @copyright Copyright (c) 2021, WeRePack.org
 * @license   GPL-2.0+
 *
 */
?>
<div class="repack-summary">
    <h2>
        <?php printf(
            /* translators: With your support we saved %s packaging, yet */
            __( 'We already saved %s with you!', 'repack' ),
            $saving->packaging
        ); ?>
    </h2>

    <p>
        <?php printf(
            /* translators: Your Sitename & WeRePack.org */
            __( '%s supports the %s initiative for packaging waste reduction. This means we reduce waste as much as possible in all business areas.', 'repack' ),
            get_bloginfo('name'),
            '<a href="https://WeRePack.org/" title="WeRePack.org" target="_blank">WeRePack.org</a>'
        ); ?>
        <?php printf(
            /* translators: %s is amount of packaging */
            __( 'Together with our customers, we have also been able to reuse %s for shipping. This means we roughly saved:', 'repack' ),
            '<b>' . $saving->packaging . '</b>'
        ); ?>
    </p>
    <ul>
        <li><b><?php echo $saving->co2; ?></b></li>
        <li><b><?php echo $saving->water; ?></b></li>
        <li><b><?php echo $saving->trees; ?></b></li>
    </ul>
    <p>
        <?php printf(
            /* translators: With your support we saved %s packaging, yet */
            __( 'You want to join? During checkout, you are free to choose that you also want to receive reused packaging. Just agree and your order will be shipped within the %s reused packaging.', 'repack' ),
            $saving->packaging + 1
        ); ?>
    </p>
</div>
