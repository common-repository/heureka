# WPify Post Type

Abstraction over WordPress Post Types.

## Installation

`composer require wpify/post-type`

## Usage

```php
class MyCustomPostType extends Wpify\PostType\AbstractCustomPostType {
    const KEY = 'my-custom-post-type';
    
    public function setup() {
        add_action( 'init', array( $this, 'do_something' ) );
    }
    
	public function get_post_type_key(): string {
		return self::KEY;
	}
	
    public function get_args(): array {
		return array(
			'label'        => __( 'My CPT', 'my-plugin' ),
			'labels'       => $this->generate_labels( __( 'My CPT', 'my-plugin' ), __( 'My CPTs', 'my-plugin' ) ),
			'description'  => __( 'Custom post type My CPT', 'my-plugin' ),
			'public'       => true,
			'show_ui'      => true,
			'show_in_rest' => true,
		);
    }
    
    public function do_something() {
        // TODO: Do something
    }
}

class MyBuiltinPagePostType extends Wpify\PostType\AbstractCustomPostType {
    const KEY = 'page';
    
    public function setup() {
        add_action( 'init', array( $this, 'do_something' ) );
    }
    
	public function get_post_type_key(): string {
		return self::KEY;
	}
    
    public function do_something() {
        // TODO: Do something
    }
} 

function my_plugin_init() {
    new MyCustomPostType;
    new MyBuiltinPagePostType;
}

add_action( 'plugins_loaded', 'my_plugin_init', 11 );
```