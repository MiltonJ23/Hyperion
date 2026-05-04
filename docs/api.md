# API Reference

Base URL: `http://<host>:8000`

All protected routes require a JWT bearer token in the `Authorization` header:

```
Authorization: Bearer <token>
```

Responses are JSON. Error responses follow the format `{"error": "<message>"}` or a validation error map `{"field": ["message"]}` with status 422.

---

## Authentication

### POST /login

Authenticate a user and obtain a JWT token.

**Request body:**
```json
{
  "email": "user@example.com",
  "password": "secret"
}
```

**Response 200:**
```json
{
  "access_token": "<jwt>",
  "token_type": "bearer",
  "expires_in": 3600,
  "user": { ... }
}
```

### POST /logout

Protected. Invalidate the current token.

### POST /refresh

Protected. Issue a new token from the current one.

### GET /me

Protected. Return the authenticated user object.

---

## Users

### POST /user

Register a new user account.

**Request body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "secret",
  "sex": "Male",
  "age": 30
}
```

**Response 201:** Created user object.

### GET /user

Protected. List all users.

### GET /user/{id}

Protected. Return a single user.

### POST /user/{id}

Protected. Update a user.

### DELETE /user/{id}

Protected. Delete a user.

### GET /user/{id}/cards

Protected. List payment cards associated with the user.

### GET /user/{id}/bookings

Protected. List all bookings for the user.

### GET /user/{userId}/event/{eventId}/booking

Protected. Return the booking record for a specific user/event pair.

### GET /user/{id}/events

Protected. List events created by the user.

### POST /user/{id}/image

Protected. Upload a profile image (`multipart/form-data`, field `image`).

---

## Events

### GET /event

Public. List upcoming events.

Query parameters:

| Parameter | Type | Description |
|---|---|---|
| `name` | string | Filter by event name (partial match) |
| `location` | string | Filter by location (partial match) |
| `date` | date (Y-m-d) | Filter by exact date |
| `max_price` | decimal | Filter by maximum price |
| `sort_by` | string | Sort field: `event_date`, `event_price`, `event_name` |
| `direction` | string | Sort direction: `asc` (default), `desc` |

Results are paginated at 6 per page. Only events with `event_date >= today` are returned.

**Response 200:** Paginated collection of event objects, each including the owning user and associated images.

### GET /event/{id}

Public. Return a single event with its owner and images.

### POST /event

Protected. Create a new event.

**Request body:**
```json
{
  "event_name": "Jazz Night",
  "event_desc": "An evening of live jazz.",
  "event_date": "2025-09-15",
  "event_time": "20:00",
  "event_venue": "Blue Note Club",
  "event_location": "Montreal, QC",
  "event_status": "Waiting",
  "event_price": 35.00,
  "fk_user_id": "<uuid>"
}
```

`event_status` must be one of `Waiting`, `In Progress`, `Finished`.

**Response 201:** Created event object.

### PUT /event/{id}

Protected. Update an event. Only the event owner can update it (403 otherwise). All fields are optional (`sometimes` validation).

### POST /event/{id}/book

Protected. Book the authenticated user for an event. Returns 400 if the event date is in the past.

### POST /event/{id}/cancel

Protected. Cancel a booking for a user.

### POST /event/{id}/image

Protected. Attach an image to an event (`multipart/form-data`, field `image`). Accepted formats: jpeg, png, jpg, gif. Maximum size: 30 MB.

### DELETE /event/{id}/image/{imageId}

Protected. Remove an image from an event and delete the file.

### GET /event/{id}/images

Protected. List images attached to an event.

### GET /event/{id}/bookings

Protected. List users who have booked an event.

---

## Cards

### GET /card

Protected. List all payment cards.

### POST /card

Protected. Add a payment card.

### GET /card/{id}

Protected. Return a single card.

### DELETE /card/{id}

Protected. Delete a card.

### GET /card/{id}/bookings

Protected. List bookings associated with a card.

---

## Cart

### GET /cart

Protected. Return the current user's cart.

### POST /cart/events/{event}

Protected. Add an event to the cart.

### DELETE /cart/events/{event}

Protected. Remove an event from the cart.

---

## Checkout

### POST /checkout

Protected. Process checkout for the current cart.

---

## Bookings

### GET /booking/{id}

Protected. Return a single booking record.
