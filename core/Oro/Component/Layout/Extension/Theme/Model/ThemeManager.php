<?php

namespace Oro\Component\Layout\Extension\Theme\Model;

/**
 * The main entry point for layout themes.
 */
class ThemeManager
{
    /** @var ThemeFactoryInterface */
    private $themeFactory;

    /** @var ThemeDefinitionBagInterface */
    private $themeDefinitionBag;

    /** @var Theme[] */
    private $instances = [];

    /**
     * @param ThemeFactoryInterface       $themeFactory
     * @param ThemeDefinitionBagInterface $themeDefinitionBag
     */
    public function __construct(
        ThemeFactoryInterface $themeFactory,
        ThemeDefinitionBagInterface $themeDefinitionBag
    ) {
        $this->themeFactory = $themeFactory;
        $this->themeDefinitionBag = $themeDefinitionBag;
    }

    /**
     * Returns all known themes names
     *
     * @return string[]
     */
    public function getThemeNames()
    {
        return $this->themeDefinitionBag->getThemeNames();
    }

    /**
     * Check whether given theme is known by manager
     *
     * @param string $themeName
     *
     * @return bool
     */
    public function hasTheme($themeName)
    {
        return null !== $this->themeDefinitionBag->getThemeDefinition($themeName);
    }

    /**
     * Gets theme model instance
     *
     * @param string $themeName
     *
     * @return Theme
     */
    public function getTheme($themeName)
    {
        if (empty($themeName)) {
            throw new \InvalidArgumentException('The theme name must not be empty.');
        }
        if (!$this->hasTheme($themeName)) {
            throw new \LogicException(sprintf('Unable to retrieve definition for theme "%s".', $themeName));
        }

        if (!isset($this->instances[$themeName])) {
            $theme = $this->themeFactory->create(
                $themeName,
                $this->themeDefinitionBag->getThemeDefinition($themeName)
            );
            $this->instances[$themeName] = $this->mergePageTemplates($theme);
        }

        return $this->instances[$themeName];
    }

    /**
     * @param Theme $theme
     *
     * @return Theme
     */
    private function mergePageTemplates(Theme $theme)
    {
        if ($theme->getParentTheme()) {
            $parentTheme = $this->getTheme($theme->getParentTheme());

            foreach ($parentTheme->getPageTemplates() as $parentPageTemplate) {
                $theme->addPageTemplate($parentPageTemplate);
            }

            foreach ($parentTheme->getPageTemplateTitles() as $route => $title) {
                if (!$theme->getPageTemplateTitle($route)) {
                    $theme->addPageTemplateTitle($route, $title);
                }
            }
        }

        return $theme;
    }

    /**
     * @param null|string|array $groups
     *
     * @return Theme[]
     */
    public function getAllThemes($groups = null)
    {
        $names = $this->getThemeNames();

        $themes = array_combine(
            $names,
            array_map(
                function ($themeName) {
                    return $this->getTheme($themeName);
                },
                $names
            )
        );

        if (!empty($groups)) {
            $groups = is_array($groups) ? $groups : [$groups];
            $themes = array_filter(
                $themes,
                function (Theme $theme) use ($groups) {
                    return count(array_intersect($groups, $theme->getGroups())) > 0;
                }
            );
        }

        return $themes;
    }
}
