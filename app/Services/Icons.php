<?php
namespace App\Services;

/**
 * Inline SVG icon set (24×24 stroke icons, currentColor). Original simple geometric paths.
 */
class Icons
{
    const PATHS = [
        'shield'       => '<path d="M12 3l8 3v6c0 5-3.5 8.5-8 9-4.5-.5-8-4-8-9V6l8-3z"/>',
        'shield-check' => '<path d="M12 3l8 3v6c0 5-3.5 8.5-8 9-4.5-.5-8-4-8-9V6l8-3z"/><path d="M8.5 12l2.5 2.5 4.5-5"/>',
        'shield-alert' => '<path d="M12 3l8 3v6c0 5-3.5 8.5-8 9-4.5-.5-8-4-8-9V6l8-3z"/><path d="M12 8v5M12 16.5v.01"/>',
        'scale'        => '<path d="M12 3v18M7 21h10M5 7h14M5 7l-3 7a3 3 0 006 0L5 7zM19 7l-3 7a3 3 0 006 0l-3-7z"/>',
        'radar'        => '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><path d="M12 12l6-6"/><circle cx="12" cy="12" r="1"/>',
        'clipboard'    => '<rect x="5" y="4" width="14" height="17" rx="2"/><path d="M9 4V3h6v1M9 11h6M9 15h4"/>',
        'lock'         => '<rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 018 0v3M12 14v3"/>',
        'badge'        => '<circle cx="12" cy="9" r="6"/><path d="M8.5 14L7 21l5-3 5 3-1.5-7"/><path d="M9.5 9l1.8 1.8L14.5 7.5"/>',
        'card'         => '<rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20M6 15h4"/>',
        'refresh'      => '<path d="M20 11a8 8 0 00-14.3-4.9L4 8M4 4v4h4M4 13a8 8 0 0014.3 4.9L20 16M20 20v-4h-4"/>',
        'cpu'          => '<rect x="6" y="6" width="12" height="12" rx="2"/><rect x="9.5" y="9.5" width="5" height="5"/><path d="M9 2v3M15 2v3M9 19v3M15 19v3M2 9h3M2 15h3M19 9h3M19 15h3"/>',
        'heart'        => '<path d="M12 20s-7-4.4-7-10a4 4 0 017-2.6A4 4 0 0119 10c0 5.6-7 10-7 10z"/><path d="M8 12h2l1-2 2 4 1-2h2"/>',
        'globe'        => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 3 2.5 15 0 18M12 3c-2.5 3-2.5 15 0 18"/>',
        'flag'         => '<path d="M5 21V4M5 4h11l-2 4 2 4H5"/>',
        'layers'       => '<path d="M12 3l9 5-9 5-9-5 9-5zM3 13l9 5 9-5"/>',
        'stack'        => '<ellipse cx="12" cy="6" rx="8" ry="3"/><path d="M4 6v6c0 1.7 3.6 3 8 3s8-1.3 8-3V6M4 12v6c0 1.7 3.6 3 8 3s8-1.3 8-3v-6"/>',
        'settings'     => '<circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M4.2 4.2l2.1 2.1M17.7 17.7l2.1 2.1M2 12h3M19 12h3M4.2 19.8l2.1-2.1M17.7 6.3l2.1-2.1"/>',
        'grid'         => '<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>',
        'browser'      => '<rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 9h18M7 6.5h.01M10 6.5h.01"/>',
        'phone'        => '<rect x="7" y="2" width="10" height="20" rx="2"/><path d="M11 18h2"/>',
        'code'         => '<path d="M8 7l-5 5 5 5M16 7l5 5-5 5M14 4l-4 16"/>',
        'network'      => '<rect x="9" y="2" width="6" height="5" rx="1"/><rect x="2" y="17" width="6" height="5" rx="1"/><rect x="16" y="17" width="6" height="5" rx="1"/><path d="M12 7v5M5 17v-2.5h14V17M12 12v2.5"/>',
        'cloud'        => '<path d="M7 18a5 5 0 01-.6-10A6 6 0 0118 9a4.5 4.5 0 01-.5 9H7z"/>',
        'server'       => '<rect x="3" y="3" width="18" height="7" rx="2"/><rect x="3" y="14" width="18" height="7" rx="2"/><path d="M7 6.5h.01M7 17.5h.01M11 6.5h6M11 17.5h6"/>',
        'search'       => '<circle cx="11" cy="11" r="7"/><path d="M20 20l-4-4"/>',
        'target'       => '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1.5"/>',
        'sliders'      => '<path d="M4 6h10M18 6h2M4 12h4M12 12h8M4 18h12M20 18h0"/><circle cx="16" cy="6" r="2"/><circle cx="10" cy="12" r="2"/><circle cx="18" cy="18" r="2"/>',
        'file-code'    => '<path d="M14 3H6a2 2 0 00-2 2v14a2 2 0 002 2h12a2 2 0 002-2V9l-6-6z"/><path d="M14 3v6h6M10 13l-2 2 2 2M14 13l2 2-2 2"/>',
        'file-text'    => '<path d="M14 3H6a2 2 0 00-2 2v14a2 2 0 002 2h12a2 2 0 002-2V9l-6-6z"/><path d="M14 3v6h6M8 13h8M8 17h5"/>',
        'file-search'  => '<path d="M14 3H6a2 2 0 00-2 2v14a2 2 0 002 2h5M14 3v6h6v3"/><circle cx="16.5" cy="16.5" r="3"/><path d="M21 21l-2.3-2.3"/>',
        'key'          => '<circle cx="8" cy="15" r="4"/><path d="M11 12l9-9M17 6l3 3M14 9l2 2"/>',
        'git'          => '<circle cx="6" cy="6" r="2.5"/><circle cx="6" cy="18" r="2.5"/><circle cx="18" cy="9" r="2.5"/><path d="M6 8.5v7M18 11.5c0 3-4 3.5-10 5"/>',
        'activity'     => '<path d="M3 12h4l3-8 4 16 3-8h4"/>',
        'users'        => '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20a6.5 6.5 0 0113 0M16 4.5a3.5 3.5 0 010 7M18 14a6 6 0 013.5 6"/>',
        'user'         => '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0116 0"/>',
        'chart'        => '<path d="M3 3v18h18"/><path d="M7 15l4-4 3 3 6-7"/>',
        'eye'          => '<path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/>',
        'database'     => '<ellipse cx="12" cy="5" rx="8" ry="3"/><path d="M4 5v14c0 1.7 3.6 3 8 3s8-1.3 8-3V5M4 12c0 1.7 3.6 3 8 3s8-1.3 8-3"/>',
        'truck'        => '<path d="M2 6h12v10H2zM14 10h4l3 3v3h-7"/><circle cx="6" cy="18" r="2"/><circle cx="17" cy="18" r="2"/>',
        'checklist'    => '<path d="M10 6h11M10 12h11M10 18h11M3 6l1.5 1.5L7 5M3 12l1.5 1.5L7 11M3 18l1.5 1.5L7 17"/>',
        'compass'      => '<circle cx="12" cy="12" r="9"/><path d="M15.5 8.5l-2 5-5 2 2-5 5-2z"/>',
        'bank'         => '<path d="M3 9l9-5 9 5M4 9v10M20 9v10M8 12v5M12 12v5M16 12v5M2 21h20"/>',
        'rocket'       => '<path d="M5 15c-1 1-1.5 4-1.5 4s3-.5 4-1.5M9 15l-2-2c1-5 5-10 13-10 0 8-5 12-10 13z"/><circle cx="14.5" cy="9.5" r="1.5"/>',
        'book'         => '<path d="M4 4.5A2.5 2.5 0 016.5 2H20v18H6.5A2.5 2.5 0 004 22.5v-18z"/><path d="M4 19.5A2.5 2.5 0 016.5 17H20"/>',
        'factory'      => '<path d="M2 21V10l6 4V10l6 4V6l4-3v18H2zM6 17h2M11 17h2"/>',
        'cart'         => '<circle cx="9" cy="20" r="1.5"/><circle cx="18" cy="20" r="1.5"/><path d="M2 3h3l2.5 12h12L22 7H6"/>',
        'briefcase'    => '<rect x="2" y="7" width="20" height="14" rx="2"/><path d="M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2M2 13h20"/>',
        'building'     => '<rect x="4" y="2" width="16" height="20" rx="1"/><path d="M9 22v-4h6v4M8 6h.01M12 6h.01M16 6h.01M8 10h.01M12 10h.01M16 10h.01M8 14h.01M12 14h.01M16 14h.01"/>',
        'arrow-right'  => '<path d="M5 12h14M13 6l6 6-6 6"/>',
        'arrow-up-right' => '<path d="M7 17L17 7M8 7h9v9"/>',
        'check'        => '<path d="M5 12.5l4.5 4.5L19 7.5"/>',
        'check-circle' => '<circle cx="12" cy="12" r="9"/><path d="M8 12.5l2.7 2.7L16 10"/>',
        'menu'         => '<path d="M3 6h18M3 12h18M3 18h18"/>',
        'close'        => '<path d="M6 6l12 12M18 6L6 18"/>',
        'mail'         => '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="M2 7l10 7 10-7"/>',
        'phone-call'   => '<path d="M5 3h4l2 5-2.5 1.5a11 11 0 005 5L15 12l5 2v4a2 2 0 01-2 2A16 16 0 013 5a2 2 0 012-2z"/>',
        'map-pin'      => '<path d="M12 21s-7-6.5-7-12a7 7 0 0114 0c0 5.5-7 12-7 12z"/><circle cx="12" cy="9" r="2.5"/>',
        'clock'        => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
        'calendar'     => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4"/>',
        'chevron-down' => '<path d="M6 9l6 6 6-6"/>',
        'chevron-left' => '<path d="M15 6l-6 6 6 6"/>',
        'chevron-right'=> '<path d="M9 6l6 6-6 6"/>',
        'plus'         => '<path d="M12 5v14M5 12h14"/>',
        'minus'        => '<path d="M5 12h14"/>',
        'quote'        => '<path d="M7 17c-2 0-3-1.5-3-3.5C4 10 6 7 9 6M17 17c-2 0-3-1.5-3-3.5C14 10 16 7 19 6"/>',
        'alert'        => '<path d="M12 3l10 18H2L12 3z"/><path d="M12 10v5M12 18v.01"/>',
        'info'         => '<circle cx="12" cy="12" r="9"/><path d="M12 11v6M12 7.5v.01"/>',
        'star'         => '<path d="M12 3l2.7 5.6 6.1.9-4.4 4.3 1 6.1L12 17l-5.4 2.9 1-6.1L3.2 9.5l6.1-.9L12 3z"/>',
        'home'         => '<path d="M3 11l9-8 9 8M5 9.5V21h14V9.5"/>',
        'inbox'        => '<path d="M3 13l3-9h12l3 9v7H3v-7zM3 13h5l1 3h6l1-3h5"/>',
        'logout'       => '<path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/>',
        'tag'          => '<path d="M3 3h8l10 10-8 8L3 11V3z"/><circle cx="7.5" cy="7.5" r="1.5"/>',
        'edit'         => '<path d="M4 20h4L19 9l-4-4L4 16v4zM14 6l4 4"/>',
        'trash'        => '<path d="M4 7h16M10 11v6M14 11v6M6 7l1 13h10l1-13M9 7V4h6v3"/>',
        'external'     => '<path d="M14 4h6v6M20 4l-9 9M18 14v6H4V6h6"/>',
        'download'     => '<path d="M12 3v12M7 10l5 5 5-5M4 21h16"/>',
        'sparkle'      => '<path d="M12 3l2 6 6 2-6 2-2 6-2-6-6-2 6-2 2-6z"/>',
        'linkedin'     => '<rect x="3" y="3" width="18" height="18" rx="3"/><path d="M8 10v7M8 7v.01M12 17v-4a2 2 0 014 0v4M12 10v7"/>',
        'facebook'     => '<path d="M14 8h3V4h-3a4 4 0 00-4 4v2H7v4h3v7h4v-7h3l1-4h-4V8z"/>',
        'instagram'    => '<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><path d="M17.5 6.5v.01"/>',
        'x'            => '<path d="M4 4l16 16M20 4L4 20"/>',
        'youtube'      => '<rect x="2" y="5" width="20" height="14" rx="4"/><path d="M10 9l5 3-5 3V9z"/>',
        'whatsapp'     => '<path d="M4 20l1.3-4A8.5 8.5 0 1112 20.5a8.4 8.4 0 01-4-1L4 20z"/><path d="M9 8.5c0 3.5 3 6.5 6.5 6.5l1-1.5-2-1-1 1c-1.2-.5-2.5-1.8-3-3l1-1-1-2L9 8.5z"/>',
        'share'        => '<circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="M8.6 13.5l6.8 4M15.4 6.5l-6.8 4"/>',
        'link'         => '<path d="M10 14a4 4 0 005.7 0l3-3a4 4 0 00-5.7-5.7l-1 1M14 10a4 4 0 00-5.7 0l-3 3a4 4 0 005.7 5.7l1-1"/>',
    ];

    public static function get(string $name, string $class = 'icon'): string
    {
        $path = self::PATHS[$name] ?? self::PATHS['shield'];
        return '<svg class="' . e($class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $path . '</svg>';
    }

    public static function names(): array
    {
        return array_keys(self::PATHS);
    }
}
