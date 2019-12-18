<?php

namespace Drupal\checklistapiexample\Plugin\Checklist;

use Drupal\checklistapi\ChecklistBase;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;

/**
 * Provides an example implementation of Checklist API.
 *
 * @Checklist(
 *   id = "example_checklist",
 *   title = @Translation("Checklist API example"),
 *   storage = "checklistapi_storage.state",
 * )
 */
final class Example extends ChecklistBase {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  protected function items(): array {
    return [
      'i_suck' => [
        '#title' => $this->t('I suck'),
        '#description' => $this->t('<p>Gain these skills to pass the <em><a href="http://headrush.typepad.com/creating_passionate_users/2005/10/getting_users_p.html">suck threshold</a></em> and start being creative with Drupal.</p>'),
        'install_configure' => [
          '#title' => $this->t('Installation and configuration of Drupal core'),
          '#description' => $this->t('Prepare for installation, run the installation script, and take the steps that should be done after the installation script has completed.'),
          'handbook_page' => [
            '#text' => $this->t('Installation Guide'),
            '#url' => Url::fromUri('http://drupal.org/documentation/install'),
          ],
        ],
        'node_system' => [
          '#title' => $this->t('Node system'),
          '#description' => $this->t('Perform a variety of operations on one or more nodes.'),
          'handbook_page' => [
            '#text' => $this->t('Manage nodes'),
            '#url' => Url::fromUri('http://drupal.org/node/306808'),
          ],
        ],
        'block_system' => [
          '#title' => $this->t('Block system'),
          '#description' => $this->t('Create blocks and adjust their appearance, shape, size and position.'),
          'handbook_page' => [
            '#text' => $this->t('Working with blocks (content in regions)'),
            '#url' => Url::fromUri('http://drupal.org/documentation/modules/block'),
          ],
        ],
        'users' => [
          '#title' => $this->t('Users, roles and permissions'),
          '#description' => $this->t('Create and manage users and access control.'),
          'handbook_page' => [
            '#text' => $this->t('Managing users'),
            '#url' => Url::fromUri('http://drupal.org/node/627158'),
          ],
        ],
        'contrib' => [
          '#title' => $this->t('Installing contributed themes and modules'),
          '#description' => $this->t('Customize Drupal to your tastes by adding modules and themes.'),
          'handbook_page' => [
            '#text' => $this->t('Installing modules and themes'),
            '#url' => Url::fromUri('http://drupal.org/documentation/install/modules-themes'),
          ],
        ],
      ],
      'i_get_by' => [
        '#title' => $this->t('I get by'),
        '#description' => $this->t('<p>Gain these skills to pass the <em><a href="http://headrush.typepad.com/creating_passionate_users/2005/10/getting_users_p.html">passion threshold</a></em> and start kicking butt with Drupal.</p>'),
        'upgrade_patch_monitor' => [
          '#title' => $this->t('Upgrading, patching, (security) monitoring'),
          'handbook_page_upgrading' => [
            '#text' => $this->t('Upgrading from previous versions'),
            '#url' => Url::fromUri('http://drupal.org/upgrade'),
          ],
          'handbook_page_patching' => [
            '#text' => $this->t('Applying patches'),
            '#url' => Url::fromUri('http://drupal.org/patch/apply'),
          ],
          'security_advisories' => [
            '#text' => $this->t('Security advisories'),
            '#url' => Url::fromUri('http://drupal.org/security'),
          ],
          'handbook_page_monitoring' => [
            '#text' => $this->t('Monitoring a site'),
            '#url' => Url::fromUri('http://drupal.org/node/627162'),
          ],
        ],
        'navigation_menus_taxonomy' => [
          '#title' => $this->t('Navigation, menus, taxonomy'),
          'handbook_page_menus' => [
            '#text' => $this->t('Working with Menus'),
            '#url' => Url::fromUri('http://drupal.org/documentation/modules/menu'),
          ],
          'handbook_page_taxonomy' => [
            '#text' => $this->t('Organizing content with taxonomy'),
            '#url' => Url::fromUri('http://drupal.org/documentation/modules/taxonomy'),
          ],
        ],
        'locale_i18n' => [
          '#title' => $this->t('Locale and internationalization'),
          'handbook_page' => [
            '#text' => $this->t('Multilingual Guide'),
            '#url' => Url::fromUri('http://drupal.org/documentation/multilingual'),
          ],
        ],
        'customize_front_page' => [
          '#title' => $this->t('Drastically customize front page'),
          'handbook_page' => [
            '#text' => $this->t('Totally customize the LOOK of your front page'),
            '#url' => Url::fromUri('http://drupal.org/node/317461'),
          ],
        ],
        'theme_modification' => [
          '#title' => $this->t('Theme and template modifications'),
          'handbook_page' => [
            '#text' => $this->t('Theming Guide'),
            '#url' => Url::fromUri('http://drupal.org/documentation/theme'),
          ],
        ],
      ],
      'i_kick_butt' => [
        '#title' => $this->t('I kick butt'),
        'contribute_docs_support' => [
          '#title' => $this->t('Contributing documentation and support'),
          'handbook_page_docs' => [
            '#text' => $this->t('Contribute to documentation'),
            '#url' => Url::fromUri('http://drupal.org/contribute/documentation'),
          ],
          'handbook_page_support' => [
            '#text' => $this->t('Provide online support'),
            '#url' => Url::fromUri('http://drupal.org/contribute/support'),
          ],
        ],
        'content_types_views' => [
          '#title' => $this->t('Content types and views'),
          'handbook_page_content_types' => [
            '#text' => $this->t('Working with nodes, content types and fields'),
            '#url' => Url::fromUri('http://drupal.org/node/717120'),
          ],
          'handbook_page_views' => [
            '#text' => $this->t('Working with Views'),
            '#url' => Url::fromUri('http://drupal.org/documentation/modules/views'),
          ],
        ],
        'actions_workflows' => [
          '#title' => $this->t('Actions and workflows'),
          'handbook_page' => [
            '#text' => $this->t('Actions and Workflows'),
            '#url' => Url::fromUri('http://drupal.org/node/924538'),
          ],
        ],
        'development' => [
          '#title' => $this->t('Theme and module development'),
          'handbook_page_theming' => [
            '#text' => $this->t('Theming Guide'),
            '#url' => Url::fromUri('http://drupal.org/documentation/theme'),
          ],
          'handbook_page_development' => [
            '#text' => $this->t('Develop for Drupal'),
            '#url' => Url::fromUri('http://drupal.org/documentation/develop'),
          ],
        ],
        'advanced_tasks' => [
          '#title' => $this->t('jQuery, Form API, security audits, performance tuning'),
          'handbook_page_jquery' => [
            '#text' => $this->t('JavaScript and jQuery'),
            '#url' => Url::fromUri('http://drupal.org/node/171213'),
          ],
          'handbook_page_form_api' => [
            '#text' => $this->t('Form API'),
            '#url' => Url::fromUri('http://drupal.org/node/37775'),
          ],
          'handbook_page_security' => [
            '#text' => $this->t('Securing your site'),
            '#url' => Url::fromUri('http://drupal.org/security/secure-configuration'),
          ],
          'handbook_page_performance' => [
            '#text' => $this->t('Managing site performance'),
            '#url' => Url::fromUri('http://drupal.org/node/627252'),
          ],
        ],
        'contribute_code' => [
          '#title' => $this->t('Contributing code, designs and patches back to Drupal'),
          'handbook_page' => [
            '#text' => $this->t('Contribute to development'),
            '#url' => Url::fromUri('http://drupal.org/contribute/development'),
          ],
        ],
        'professional' => [
          '#title' => $this->t('Drupal consultant or working for a Drupal shop'),
        ],
        'chx_or_unconed' => [
          '#title' => $this->t(
            'I\'m a <a href=":chx_url">chx</a> or <a href=":unconed_url">UnConeD</a>.',
            [
              ':chx_url' => 'http://drupal.org/user/9446',
              ':unconed_url' => 'http://drupal.org/user/10',
            ]
          ),
        ],
      ],
    ];
  }

}
