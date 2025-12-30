# 📊 Updated Data Model – with Meal ↔ Recipe Links

## 1. Users
- `id` (PK)
- `name`
- `email` (unique)
- `password`
- `role` (enum: admin, user, guest [future])
- `created_at`, `updated_at`

**Relations**
- One user → many recipes  
- One user → many meals  
- One user → many reviews  

---

## 2. Categories
- `id` (PK)
- `name`
- `parent_id` (FK → categories.id, nullable)
- `created_at`, `updated_at`

**Relations**
- One category → many recipes  
- One category → many meals  

---

## 3. Recipes
- `id` (PK)
- `user_id` (FK → users.id)
- `category_id` (FK → categories.id)
- `title`
- `description` (nullable)
- `is_published` (boolean, default false)
- `created_at`, `updated_at`

**Relations**
- One recipe → many ingredients  
- One recipe → many steps  
- One recipe → many pictures  
- One recipe → many reviews  
- One recipe ↔ many meals (via meal_recipe)  

---

## 4. Ingredients
- `id` (PK)
- `recipe_id` (FK → recipes.id)
- `name`
- `quantity` (string)
- `unit` (string, nullable)
- `created_at`, `updated_at`

---

## 5. Steps
- `id` (PK)
- `recipe_id` (FK → recipes.id)
- `step_number` (int)
- `description` (text)
- `image_path` (nullable)
- `video_url` (nullable)
- `created_at`, `updated_at`

---

## 6. Recipe Pictures
- `id` (PK)
- `recipe_id` (FK → recipes.id)
- `image_path`
- `created_at`, `updated_at`

---

## 7. Meals
- `id` (PK)
- `user_id` (FK → users.id)
- `category_id` (FK → categories.id)
- `name`
- `description` (nullable)
- `restaurant_name`
- `restaurant_location` (nullable)
- `date_eaten` (nullable)
- `is_published` (boolean, default false)
- `created_at`, `updated_at`

**Relations**
- One meal → many pictures  
- One meal → many reviews  
- One meal ↔ many recipes (via meal_recipe)  

---

## 8. Meal Pictures
- `id` (PK)
- `meal_id` (FK → meals.id)
- `image_path`
- `created_at`, `updated_at`

---

## 9. Reviews
- `id` (PK)
- `user_id` (FK → users.id)
- `reviewable_id` (morph FK: recipe/meal)
- `reviewable_type` (enum: recipe, meal)
- `rating` (1–5)
- `text` (nullable)
- `created_at`, `updated_at`

---

## 10. Meal_Recipe (pivot table)
- `id` (PK)
- `meal_id` (FK → meals.id)
- `recipe_id` (FK → recipes.id)
- `created_at`, `updated_at`

---

## Diagram (Text Overview)
```
Users (1)───(∞) Recipes
Users (1)───(∞) Meals
Users (1)───(∞) Reviews

Categories (1)───(∞) Recipes
Categories (1)───(∞) Meals
Categories (1)───(∞) Categories (nested)

Recipes (1)───(∞) Ingredients
Recipes (1)───(∞) Steps
Recipes (1)───(∞) RecipePictures
Recipes (1)───(∞) Reviews
Recipes (∞)───(∞) Meals (via meal_recipe)

Meals (1)───(∞) MealPictures
Meals (1)───(∞) Reviews
Meals (∞)───(∞) Recipes (via meal_recipe)
```
