// Modern JavaScript with ES6+

// Dropdown handling
document.addEventListener('DOMContentLoaded', () => {
    // Initialize all dropdowns
    const dropdowns = document.querySelectorAll('.dropdown');
    
    dropdowns.forEach(dropdown => {
        const toggle = dropdown.querySelector('.dropdown-toggle');
        
        if (toggle) {
            toggle.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                
                // Close all other dropdowns
                dropdowns.forEach(other => {
                    if (other !== dropdown) {
                        other.classList.remove('show');
                    }
                });
                
                // Toggle current dropdown
                dropdown.classList.toggle('show');
            });
        }
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.dropdown')) {
            dropdowns.forEach(dropdown => {
                dropdown.classList.remove('show');
            });
        }
    });
});

// Cart functionality
class Cart {
    constructor() {
        this.items = [];
        this.total = 0;
        this.init();
    }
    
    init() {
        // Load cart from localStorage
        const savedCart = localStorage.getItem('cart');
        if (savedCart) {
            this.items = JSON.parse(savedCart);
            this.updateTotal();
            this.updateDisplay();
        }
        
        // Initialize delete buttons
        this.initDeleteButtons();
    }
    
    addItem(item) {
        const existingItem = this.items.find(i => i.id === item.id);
        
        if (existingItem) {
            existingItem.quantity += 1;
        } else {
            this.items.push({...item, quantity: 1});
        }
        
        this.updateCart();
    }
    
    removeItem(itemId) {
        this.items = this.items.filter(item => item.id !== itemId);
        this.updateCart();
    }
    
    updateQuantity(itemId, quantity) {
        const item = this.items.find(i => i.id === itemId);
        if (item) {
            item.quantity = parseInt(quantity);
            if (item.quantity <= 0) {
                this.removeItem(itemId);
            } else {
                this.updateCart();
            }
        }
    }
    
    updateTotal() {
        this.total = this.items.reduce((sum, item) => {
            return sum + (item.price * item.quantity);
        }, 0);
    }
    
    updateDisplay() {
        // Update cart count
        const qtyElements = document.querySelectorAll('.qty');
        const itemCount = this.items.reduce((sum, item) => sum + item.quantity, 0);
        qtyElements.forEach(el => el.textContent = itemCount);
        
        // Update cart list
        const cartList = document.querySelector('.cart-list');
        if (cartList) {
            cartList.innerHTML = this.items.map(item => `
                <div class="product-widget" data-id="${item.id}">
                    <div class="product-img">
                        <img src="${item.image}" alt="${item.name}">
                    </div>
                    <div class="product-body">
                        <h3 class="product-name"><a href="#">${item.name}</a></h3>
                        <h4 class="product-price">
                            <span class="qty">${item.quantity}x</span>$${item.price.toFixed(2)}
                        </h4>
                    </div>
                    <button class="delete"><i class="fa fa-close"></i></button>
                </div>
            `).join('');
            
            // Update cart summary
            const cartSummary = document.querySelector('.cart-summary');
            if (cartSummary) {
                cartSummary.innerHTML = `
                    <small>${itemCount} Item(s) selected</small>
                    <h5>SUBTOTAL: $${this.total.toFixed(2)}</h5>
                `;
            }
            
            this.initDeleteButtons();
        }
    }
    
    updateCart() {
        this.updateTotal();
        this.updateDisplay();
        localStorage.setItem('cart', JSON.stringify(this.items));
    }
    
    initDeleteButtons() {
        const deleteButtons = document.querySelectorAll('.product-widget .delete');
        deleteButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                const widget = e.target.closest('.product-widget');
                if (widget) {
                    const itemId = widget.dataset.id;
                    this.removeItem(itemId);
                }
            });
        });
    }
}

// Initialize cart
const cart = new Cart();

// Add to cart functionality
document.addEventListener('DOMContentLoaded', () => {
    const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
    
    addToCartButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            
            const productCard = e.target.closest('.product');
            if (productCard) {
                const item = {
                    id: productCard.dataset.id,
                    name: productCard.querySelector('.product-name a').textContent,
                    price: parseFloat(productCard.querySelector('.product-price').textContent.replace('$', '')),
                    image: productCard.querySelector('.product-img img').src
                };
                
                cart.addItem(item);
                
                // Show success message
                const toast = document.createElement('div');
                toast.className = 'toast fade-in';
                toast.textContent = 'Item added to cart!';
                document.body.appendChild(toast);
                
                setTimeout(() => {
                    toast.remove();
                }, 3000);
            }
        });
    });
}); 