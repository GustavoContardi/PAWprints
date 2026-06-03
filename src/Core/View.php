<?php

namespace Core;

class View
{
    private static ?\Twig\Environment $twig = null;

    public static function getTwig(): \Twig\Environment
    {
        if (self::$twig === null) {
            self::$twig = TwigFactory::create();
        }
        return self::$twig;
    }

    public static function render(string $view, array $data = []): void
    {
        echo self::getTwig()->render($view . '.twig', $data);
    }
}