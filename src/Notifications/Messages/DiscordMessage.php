<?php declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Notifications\Messages;

use Carbon\Carbon;

/**
 * Class     DiscordMessage
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class DiscordMessage
{
    /* -----------------------------------------------------------------
     |  Constants
     | -----------------------------------------------------------------
     */

    public const COLOR_SUCCESS = '0b6623';
    public const COLOR_WARNING = 'fD6a02';
    public const COLOR_ERROR = 'e32929';

    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /**
     * @var string
     */
    protected $username = 'Laravel Backup';

    /**
     * @var string|null
     */
    protected $avatarUrl = null;

    /**
     * @var string
     */
    protected $title = '';

    /**
     * @var string
     */
    protected $description = '';

    /**
     * @var array
     */
    protected $fields = [];

    /**
     * @var string|null
     */
    protected $timestamp = null;

    /**
     * @var string|null
     */
    protected $footer = null;

    /**
     * @var string|null
     */
    protected $color = null;

    /**
     * @var string
     */
    protected $url = '';

    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */

    /**
     * @param  string       $username
     * @param  string|null  $avatarUrl
     *
     * @return $this
     */
    public function from(string $username, string $avatarUrl = null): self
    {
        if ( ! is_null($username))
            $this->username = $username;

        if ( ! is_null($avatarUrl))
            $this->avatarUrl = $avatarUrl;

        return $this;
    }

    /**
     * @param  string  $url
     *
     * @return $this
     */
    public function url(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @param  string  $title
     *
     * @return $this
     */
    public function title(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @param  string  $description
     *
     * @return $this
     */
    public function description(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @param \Illuminate\Support\Carbon $carbon
     *
     * @return $this
     */
    public function timestamp(Carbon $carbon): self
    {
        $this->timestamp = $carbon->toIso8601String();

        return $this;
    }

    /**
     * @param  string  $footer
     *
     * @return $this
     */
    public function footer(string $footer): self
    {
        $this->footer = $footer;

        return $this;
    }

    /**
     * Set the success color.
     *
     * @return $this
     */
    public function success(): self
    {
        $this->color = static::COLOR_SUCCESS;

        return $this;
    }

    /**
     * Set the warning color.
     *
     * @return $this
     */
    public function warning(): self
    {
        $this->color = static::COLOR_WARNING;

        return $this;
    }

    /**
     * Set the error color.
     *
     * @return $this
     */
    public function error(): self
    {
        $this->color = static::COLOR_ERROR;

        return $this;
    }

    /**
     * Set the fields.
     *
     * @param  array  $fields
     * @param  bool   $inline
     *
     * @return $this
     */
    public function fields(array $fields, bool $inline = true): self
    {
        foreach ($fields as $label => $value) {
            $this->fields[] = [
                'name'   => $label,
                'value'  => $value,
                'inline' => $inline,
            ];
        }

        return $this;
    }

    /**
     * Convert the message instance into an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'username'   => 'Laravel Backup',
            'avatar_url' => '',
            'embeds'     => [
                [
                    'title'       => $this->title,
                    'url'         => $this->url,
                    'type'        => 'rich',
                    'description' => $this->description,
                    'fields'      => $this->fields,
                    'color'       => hexdec($this->color),
                    'footer'      => [
                        'text' => $this->footer ?? '',
                    ],
                    'timestamp'   => $this->timestamp ?? Carbon::now(),
                ],
            ],
        ];
    }
}
