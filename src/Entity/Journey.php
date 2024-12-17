<?php

namespace App\Entity;

use App\Repository\JourneyRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[Vich\Uploadable]
#[Gedmo\SoftDeleteable(fieldName: "deletedAt", timeAware: false, hardDelete: false)]
#[ORM\Entity(repositoryClass: JourneyRepository::class)]
class Journey
{

    use SoftDeleteableEntity;
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $stage = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[Vich\UploadableField(mapping: 'gpxFiles', fileNameProperty: 'gpxName')]
    private ?File $gpxFile = null;

    #[ORM\Column(nullable: true)]
    private ?string $gpxName = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStage(): ?string
    {
        return $this->stage;
    }

    public function setStage(string $stage): static
    {
        $this->stage = $stage;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function setGpxFile(?File $gpxFile = null): void
    {
        $this->gpxFile = $gpxFile;

        if (null !== $gpxFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getGpxFile(): ?File
    {
        return $this->gpxFile;
    }

    public function setGpxName(?string $gpxName): void
    {
        $this->gpxName = $gpxName;
    }

    public function getGpxName(): ?string
    {
        return $this->gpxName;
    }

}
