<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BlogPostRepository")
 * @ApiResource(
 *     attributes={"order"={"published":"DESC"}},
 *     itemOperations={
 *          "get"={
 *              "normalization_context"={
 *                  "groups"={"get-blog-with-author"}
 *          }
 *     },
 *          "put"={
 *              "access_control"="is_granted('ROLE_EDITOR') or (is_granted('ROLE_WRITER') and object.getAuthor() == user)"
 *          }
 *     },
 *     collectionOperations={
 *          "get",
 *          "post"={
 *              "access_control"="is_granted('ROLE_WRITER')"
 *          }
 *      },
 *     denormalizationContext={
 *      "groups" = {"post"}
 *     }
 *  )
 */
class BlogPost implements AuthoredEntityInterface, PublishedDateEntityInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"get-blog-with-author"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Groups({"post", "get-blog-with-author"})
     * @Assert\Length(min="10")
     */
    private $title;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"get-blog-with-author"})
     */
    private $published;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     * @Assert\Length(min="20")
     * @Groups({"post", "get-blog-with-author"})
     */
    private $content;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="posts")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"get-blog-with-author"})
     */
    private $author;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="blogPost")
     * @ApiSubresource()
     * i onda se prikazu svi comment a urlom http://127.0.0.1:8000/api/blog_posts/102/comments
     * koristit isto deboug:router
     * @Groups({"get-blog-with-author"})
     */
    private $comments;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     * @Groups({"post", "get-blog-with-author"})
     */
    private $slug;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Image")
     * join table heisst das die table image und blogpost nicht geändert sind sondern nur eine neu table mit zwei fields erstellt
     * @ORM\JoinTable()
     * @ApiSubresource()
     * @Groups({"post", "get-blog-with-author"})
     */
    private $images;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->images = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getPublished(): ?\DateTimeInterface
    {
        return $this->published;
    }

    public function setPublished(\DateTimeInterface $published): PublishedDateEntityInterface
    {
        $this->published = $published;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function setSlug($slug): void
    {
        $this->slug = $slug;
    }

    /**
     * @return mixed
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param UserInterface $author
     * @return AuthoredEntityInterface
     */
    public function setAuthor(UserInterface $author): AuthoredEntityInterface
    {
        $this->author = $author;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getComments()
    {
        return $this->comments;
    }

    public function setComments($comments): void
    {
        $this->comments = $comments;
    }

    public function getImages()
    {
        return $this->images;
    }

    public function addImage(Image $image)
    {
        $this->images->add($image);
    }

    public function removeImage(Image $image)
    {
        $this->images->removeElement($image);
    }
}
