![Demo Image](https://github.com/user-attachments/assets/0c204250-93f5-436b-ba41-1721c17e2bc3)

# Crypto Broker Trading Platform

A full-stack cryptocurrency trading application with real-time order matching, wallet management, and live orderbook functionality.

## 🚀 Tech Stack

### Backend
- **Laravel** - PHP framework for REST API
- **MySQL/PostgreSQL** - Relational database
- **Laravel Broadcasting** - Real-time events via Pusher
- **Laravel Fortify** - Authentication system

### Frontend
- **Nuxt 3** - Vue.js framework with Composition API
- **TypeScript** - Type-safe development
- **Tailwind CSS v4** - Utility-first styling
- **shadcn-vue** - High-quality Vue component library
- **Lato Font** - Modern typography

---

## ✨ Features

### Trading Engine
- **Limit Orders**: Place buy/sell orders with custom price and amount
- **Order Matching**: Automatic matching of compatible orders
- **Atomic Transactions**: Database-level consistency for balance operations
- **Commission System**: 1.5% fee on all matched trades
- **Order Management**: Cancel open orders instantly

### Wallet System
- **USD Balance**: Track available and locked USD funds
- **Crypto Assets**: Manage multiple cryptocurrency holdings (BTC, ETH, SOL, XRP, ADA, DOT, LINK, UNI, AVAX, MATIC)
- **Locked Amounts**: Automatic fund reservation for open orders
- **Real-time Updates**: Instant balance updates via Pusher

### Orderbook
- **Live Order Display**: View all open orders in the market
- **Symbol Filtering**: Switch between different trading pairs
- **Buy/Sell Separation**: Clear visualization of order types
- **Price Sorting**: Organized orders for easy analysis

### Order History
- **Complete History**: View all past and current orders
- **Status Tracking**: Open, Filled, or Cancelled statuses
- **Advanced Filtering**: Filter by symbol, side, and status
- **Instant Updates**: Real-time order status changes

---

## 🏗️ Architecture

### Database Schema

#### Users
```sql
- id
- name
- email
- balance (decimal, USD funds)
- locked_balance (decimal, reserved for orders)
- timestamps
```

#### Assets
```sql
- id
- user_id
- symbol (BTC, ETH, etc.)
- amount (decimal, available balance)
- locked_amount (decimal, reserved for sell orders)
- timestamps
```

#### Orders
```sql
- id
- user_id
- symbol (BTC, ETH, etc.)
- side (buy/sell)
- price (decimal)
- amount (decimal)
- status (Open, Filled, Cancelled)
- timestamps
```

### API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/profile` | Get user profile with balances and assets |
| GET | `/api/orders` | Get all open orders for orderbook |
| GET | `/api/profile/orders` | Get user's order history |
| POST | `/api/orders` | Create a new limit order |
| POST | `/api/orders/{id}/cancel` | Cancel an open order |
| POST | `/login` | Authenticate user |
| POST | `/logout` | Logout user |

---

## 💡 Core Business Logic

### Buy Order Flow
1. Validate user has sufficient USD balance: `balance >= amount * price`
2. Deduct `amount * price` from user's `balance`
3. Mark as "Open" with locked USD value
4. Broadcast order created event
5. Attempt to match with existing sell orders

### Sell Order Flow
1. Validate user has sufficient asset balance: `asset.amount >= order.amount`
2. Move `amount` from `asset.amount` to `asset.locked_amount`
3. Mark as "Open" with locked asset value
4. Broadcast order created event
5. Attempt to match with existing buy orders

### Order Matching Rules
- **Full Match Only**: No partial executions
- **Buy Orders**: Match with first SELL where `sell.price <= buy.price`
- **Sell Orders**: Match with first BUY where `buy.price >= sell.price`
- **Price-Time Priority**: Earliest orders at same price match first
- **Atomic Execution**: All database operations within transactions

### Commission System
- **Rate**: 1.5% of matched USD value
- **Calculation**: `commission = matched_usd_value * 0.015`
- **Example**: 0.01 BTC @ $95,000 = $950 volume, fee = $14.25
- **Deduction**: Automatically deducted from matched funds


---

## 📦 Installation

### Prerequisites
- PHP 8.2+
- Composer
- Node.js 18+
- MySQL or PostgreSQL
- Pusher account (for real-time features)

### Backend Setup

```bash
cd broker-api

# Install dependencies
composer install

# Environment configuration
cp .env.example .env

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate

# Seed database with sample users
php artisan db:seed

# Start development server
php artisan serve
```

### Frontend Setup

```bash
cd broker-ui

# Install dependencies
pnpm install

# Start development server
pnpm dev
```

### Pusher Configuration

1. Create a Pusher account at https://pusher.com
2. Create a new app with:
   - **Type**: Channels
   - **Client**: JS
3. Add credentials to backend `.env`:
   ```
   PUSHER_APP_ID=your_app_id
   PUSHER_APP_KEY=your_app_key
   PUSHER_APP_SECRET=your_app_secret
   PUSHER_APP_CLUSTER=your_cluster
   ```
4. Configure broadcasting in `config/broadcasting.php`

---

## 👤 Demo Credentials

After running `php artisan db:seed`, you can use these credentials to login:

### User 1
- **Email**: `john@doe.com`
- **Password**: `secret`
- **Initial Balance**: $50,000 USD
- **Assets**:
  - 0.5 BTC
  - 5.0 ETH

### User 2
- **Email**: `jane@doe.com`
- **Password**: `secret`
- **Initial Balance**: $50,000 USD
- **Assets**:
  - 0.8 BTC
  - 20.0 ETH

Both accounts come with pre-funded balances and crypto assets for immediate trading.

### Planned Features
- [ ] Real-time WebSocket integration in frontend
