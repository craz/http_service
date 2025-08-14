---
auto_apply: true
apply_to: ["**/*.js", "**/*.css", "**/*.html", "views/**/*"]
---

# Правила для фронтенд части

## JavaScript и jQuery

- Использовать современный синтаксис JavaScript (ES6+)
- Избегать глобальных переменных
- Использовать делегирование событий для динамического контента
- Минифицировать и объединять JS файлы для продакшена

## CSS и стили

- Использовать БЭМ методологию для именования классов
- Создавать переиспользуемые компоненты
- Использовать CSS переменные для цветовой схемы
- Оптимизировать CSS для быстрой загрузки

## CKEditor интеграция

- Настраивать тулбар под нужды проекта
- Использовать KCFinder для загрузки файлов
- Настраивать стили для контента редактора
- Валидировать контент на сервере

## Примеры кода

### Инициализация CKEditor

```javascript
$(document).ready(function () {
  if (typeof CKEDITOR !== "undefined") {
    CKEDITOR.replace("content", {
      height: 300,
      filebrowserBrowseUrl:
        "/web/tools/ckeditor/kcfinder-2.54/browse.php?type=files",
      filebrowserImageBrowseUrl:
        "/web/tools/ckeditor/kcfinder-2.54/browse.php?type=images",
      filebrowserUploadUrl:
        "/web/tools/ckeditor/kcfinder-2.54/upload.php?type=files",
      filebrowserImageUploadUrl:
        "/web/tools/ckeditor/kcfinder-2.54/upload.php?type=images",
    });
  }
});
```

### AJAX запросы

```javascript
function deleteItem(id) {
  if (confirm("Вы уверены, что хотите удалить этот элемент?")) {
    $.ajax({
      url: "/admin/item/delete",
      type: "POST",
      data: {
        id: id,
        _csrf: $("meta[name=csrf-token]").attr("content"),
      },
      success: function (response) {
        if (response.success) {
          $("#item-" + id).remove();
          showNotification("Элемент успешно удален", "success");
        } else {
          showNotification("Ошибка при удалении", "error");
        }
      },
      error: function () {
        showNotification("Произошла ошибка", "error");
      },
    });
  }
}
```

## Оптимизация производительности

- Ленивая загрузка изображений
- Использование CDN для статических ресурсов
- Сжатие изображений
- Минификация CSS и JS файлов
