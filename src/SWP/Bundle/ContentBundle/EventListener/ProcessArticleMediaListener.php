<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\EventListener;

use SWP\Bundle\ContentBundle\Event\ArticleEvent;

class ProcessArticleMediaListener extends AbstractArticleMediaListener
{
    public function onArticleCreate(ArticleEvent $event): void
    {
        $package = $event->getPackage();
        $article = $event->getArticle();

        if (null === $package || (null !== $package && 0 === \count($package->getItems()))) {
            return;
        }

        $this->removeOldArticleMedia($article);

        foreach ($package->getItems() as $packageItem) {
            $key = $packageItem->getName();
            if ($this->isTypeAllowed($packageItem->getType())) {
                $this->removeArticleMediaIfNeeded($key, $article);

                $articleMedia = $this->handleMedia($article, $key, $packageItem);
                $this->articleMediaRepository->persist($articleMedia);
            }

            if (null !== $packageItem->getItems() && 0 !== $packageItem->getItems()->count()) {
                foreach ($packageItem->getItems() as $key => $item) {
                    if ($this->isTypeAllowed($item->getType())) {
                        $this->removeArticleMediaIfNeeded($key, $article);

                        $articleMedia = $this->handleMedia($article, $key, $item);
                        $this->articleMediaRepository->persist($articleMedia);
                    }
                }
            }
        }
    }
}
