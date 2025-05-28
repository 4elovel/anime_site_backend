# File Handling in Models

This document describes the file-related fields in each model and how they should be handled using the `HasFiles` trait.

## Anime Model

File-related fields:
- `poster`: Used for storing the anime poster image
- `image_name`: Used for storing the anime main image
- `meta_image`: Used for SEO meta image
- `attachments`: JSON field for storing various attachments (pictures, trailers, etc.)

Example usage:
```php
// Store a poster image
$anime->storeFile($request->file('poster'), 'poster', 'posters', true);

// Get poster URL
$posterUrl = $anime->getFileUrl('poster');

// Process attachments
$anime->attachments = $anime->processAttachments($request->attachments, 'anime_attachments');
$anime->save();
```

## Episode Model

File-related fields:
- `pictures`: JSON array for storing episode screenshots/images
- `meta_image`: Used for SEO meta image
- `video_players`: JSON array for storing video player information

Example usage:
```php
// Store a picture
$pictures = $episode->pictures ?? [];
if ($request->hasFile('picture')) {
    $path = $episode->fileService()->storeFile($request->file('picture'), 'episodes', null, true);
    $pictures[] = $path;
    $episode->pictures = $pictures;
    $episode->save();
}

// Get picture URL
$pictureUrl = $episode->pictureUrl;
```

## Selection Model

File-related fields:
- `meta_image`: Used for SEO meta image

Example usage:
```php
// Store a meta image
$selection->storeFile($request->file('meta_image'), 'meta_image', 'meta', true);

// Get meta image URL
$metaImageUrl = $selection->getFileUrl('meta_image');
```

## User Model

File-related fields:
- `avatar`: User profile avatar
- `backdrop`: User profile background image

Example usage:
```php
// Store an avatar
$user->storeFile($request->file('avatar'), 'avatar', 'avatar_users', true);

// Store a backdrop
$user->storeFile($request->file('backdrop'), 'backdrop', 'backdrop_users', true);

// Get avatar URL
$avatarUrl = $user->getFileUrl('avatar');
```

## Person Model

File-related fields:
- `image`: Person's profile image
- `meta_image`: Used for SEO meta image

Example usage:
```php
// Store a person image
$person->storeFile($request->file('image'), 'image', 'people', true);

// Get image URL
$imageUrl = $person->getFileUrl('image');
```

## Studio Model

File-related fields:
- `image`: Studio logo or image
- `meta_image`: Used for SEO meta image

Example usage:
```php
// Store a studio image
$studio->storeFile($request->file('image'), 'image', 'studio', true);

// Get image URL
$imageUrl = $studio->getFileUrl('image');
```
