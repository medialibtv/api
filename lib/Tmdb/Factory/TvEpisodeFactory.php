<?php
/**
 * This file is part of the Tmdb PHP API created by Michael Roterman.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Tmdb
 * @author Michael Roterman <michael@wtfz.net>
 * @copyright (c) 2013, Michael Roterman
 * @version 0.0.1
 */
namespace Tmdb\Factory;

use Tmdb\Factory\People\CastFactory;
use Tmdb\Factory\People\CrewFactory;
use Tmdb\Model\Common\GenericCollection;
use Tmdb\Model\Tv\ExternalIds;
use Tmdb\Model\Tv\Person\CastMember;
use Tmdb\Model\Tv\Person\CrewMember;
use Tmdb\Model\Tv\Episode;

class TvEpisodeFactory extends AbstractFactory {
    /**
     * @var People\CastFactory
     */
    private $castFactory;

    /**
     * @var People\CrewFactory
     */
    private $crewFactory;

    /**
     * @var ImageFactory
     */
    private $imageFactory;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->castFactory  = new CastFactory();
        $this->crewFactory  = new CrewFactory();
        $this->imageFactory = new ImageFactory();
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data = array())
    {
        if (!$data) {
            return null;
        }

        $tvEpisode = new Episode();

        if (array_key_exists('credits', $data)) {
            if (array_key_exists('cast', $data['credits'])) {
                $tvEpisode->getCredits()->setCast(
                    $this->getCastFactory()->createCollection($data['credits']['cast'],
                        new CastMember())
                );
            }

            if (array_key_exists('crew', $data['credits'])) {
                $tvEpisode->getCredits()->setCrew(
                    $this->getCrewFactory()->createCollection($data['credits']['crew'],
                    new CrewMember())
                );
            }
        }

        /** External ids */
        if (array_key_exists('external_ids', $data)) {
            $tvEpisode->setExternalIds(
                $this->hydrate(new ExternalIds(), $data['external_ids'])
            );
        }

        /** Images */
        if (array_key_exists('images', $data)) {
            $tvEpisode->setImages($this->getImageFactory()->createCollectionFromTv($data['images']));
        }

        return $this->hydrate($tvEpisode, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function createCollection(array $data = array())
    {
        $collection = new GenericCollection();

        foreach($data as $item) {
            $collection->add(null, $this->create($item));
        }

        return $collection;
    }

    /**
     * @param \Tmdb\Factory\People\CastFactory $castFactory
     * @return $this
     */
    public function setCastFactory($castFactory)
    {
        $this->castFactory = $castFactory;
        return $this;
    }

    /**
     * @return \Tmdb\Factory\People\CastFactory
     */
    public function getCastFactory()
    {
        return $this->castFactory;
    }

    /**
     * @param \Tmdb\Factory\People\CrewFactory $crewFactory
     * @return $this
     */
    public function setCrewFactory($crewFactory)
    {
        $this->crewFactory = $crewFactory;
        return $this;
    }

    /**
     * @return \Tmdb\Factory\People\CrewFactory
     */
    public function getCrewFactory()
    {
        return $this->crewFactory;
    }

    /**
     * @param \Tmdb\Factory\ImageFactory $imageFactory
     * @return $this
     */
    public function setImageFactory($imageFactory)
    {
        $this->imageFactory = $imageFactory;
        return $this;
    }

    /**
     * @return \Tmdb\Factory\ImageFactory
     */
    public function getImageFactory()
    {
        return $this->imageFactory;
    }
}