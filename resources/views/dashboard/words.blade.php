<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Современная админка</title>
    <style>
        /* Общие стили */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body {
            display: flex;
            height: 100vh;
            background: #f0f4f8;
            font-size: 16px;
            color: #333;
        }

        /* Стили для бокового меню */
        .sidebar {
            width: 250px;
            background: linear-gradient(135deg, #6c5ce7, #00b894);
            color: #fff;
            padding-top: 30px;
            height: 100%;
            position: fixed;
            left: 0;
            top: 0;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            transition: width 0.3s ease;
        }
        .sidebar h2 {
            text-align: center;
            margin-bottom: 40px;
            font-size: 28px;
            font-weight: bold;
            letter-spacing: 1px;
        }
        .sidebar ul {
            list-style: none;
            padding-left: 0;
        }
        .sidebar ul li {
            text-align: center;
            transition: background 0.3s ease;
        }
        .sidebar ul li a {
            color: #fff;
            text-decoration: none;
            font-size: 18px;
            display: block;
            padding: 10px;
        }
        .sidebar ul li a:hover {
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 5px;
            padding: 10px;
        }

        /* Основной контейнер для контента */
        .content {
            margin-left: 250px;
            padding: 30px;
            width: calc(100% - 250px);
            transition: margin-left 0.3s ease, width 0.3s ease;
        }

        /* Карточки */
        .card {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 30px;
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
        }

        /* Таблица */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        table th, table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        table th {
            background-color: #6c5ce7;
            color: #fff;
            font-weight: bold;
        }
        table tr:hover {
            background-color: #f1f1f1;
        }

        /* Кнопки */
        .btn {
            background-color: #00b894;
            color: #fff;
            border: none;
            padding: 12px 25px;
            font-size: 16px;
            border-radius: 30px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        .btn:hover {
            background-color: #55efc4;
        }
        .btn:active {
            transform: scale(0.95);
        }

        /* Формы */
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            font-size: 16px;
            margin-bottom: 5px;
            display: block;
        }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 15px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f8f8f8;
            transition: border 0.3s ease;
        }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            border-color: #00b894;
            outline: none;
        }

        /* Общие стили для всего контейнера навигации */
nav[role="navigation"] {
    padding: 1rem;
    background-color: #ffffff;
    border-radius: 0.375rem;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

nav[role="navigation"] .flex {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

/* Стили для обычных страниц (ссылки) */
nav[role="navigation"] .relative.inline-flex {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    font-weight: 500;
    text-align: center;
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    background-color: #ffffff;
    color: #4b5563;
    transition: all 0.3s ease;
}

/* При наведении на ссылки */
nav[role="navigation"] .relative.inline-flex:hover {
    color: #1f2937;
    border-color: #9ca3af;
    background-color: #f3f4f6;
}

/* При активации ссылки (для текущей страницы) */
nav[role="navigation"] .relative.inline-flex[aria-current="page"] {
    background-color: #3b82f6;
    color: white;
    border-color: #3b82f6;
}

/* Стили для кнопок Previous и Next */
nav[role="navigation"] .relative.inline-flex[aria-disabled="true"],
nav[role="navigation"] .relative.inline-flex[aria-label="Next »"],
nav[role="navigation"] .relative.inline-flex[aria-label="« Previous"] {
    background-color: #f9fafb;
    color: #9ca3af;
    cursor: not-allowed;
    border: 1px solid #e5e7eb;
}

nav[role="navigation"] .relative.inline-flex[aria-label="Next »"]:hover,
nav[role="navigation"] .relative.inline-flex[aria-label="« Previous"]:hover {
    background-color: #f3f4f6;
    border-color: #d1d5db;
}

/* Для тёмной темы (при наличии dark-класса или предпочтений в браузере) */
@media (prefers-color-scheme: dark) {
    nav[role="navigation"] {
        background-color: #2d3748;
    }
    
    nav[role="navigation"] .relative.inline-flex {
        border-color: #4a5568;
        background-color: #2d3748;
        color: #e2e8f0;
    }

    nav[role="navigation"] .relative.inline-flex:hover {
        background-color: #4a5568;
        border-color: #2d3748;
    }

    nav[role="navigation"] .relative.inline-flex[aria-current="page"] {
        background-color: #3b82f6;
        color: white;
        border-color: #3b82f6;
    }

    nav[role="navigation"] .relative.inline-flex[aria-disabled="true"] {
        background-color: #4a5568;
        color: #e2e8f0;
        border-color: #4a5568;
    }
}

/* Мобильные стили */
@media (max-width: 640px) {
    nav[role="navigation"] .flex-1 {
        flex: 1;
    }
    
    nav[role="navigation"] .flex.justify-between {
        justify-content: space-between;
    }

    nav[role="navigation"] .sm\\:hidden {
        display: flex;
        justify-content: space-between;
    }

    nav[role="navigation"] .sm\\:flex-1 {
        flex: 1;
    }
}

        /* Адаптивность */
        @media (max-width: 768px) {
            body {
                flex-direction: column;
            }
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                box-shadow: none;
            }
            .sidebar h2 {
                font-size: 24px;
            }
            .sidebar ul li {
                padding: 12px;
            }
            .sidebar ul li a {
                font-size: 16px;
            }
            .content {
                margin-left: 0;
                width: 100%;
            }
            .sidebar ul {
                display: flex;
                justify-content: space-around;
                align-items: center;
            }
        }

        @media (max-width: 600px) {
            table th, table td {
                font-size: 14px;
                padding: 10px;
            }
            .form-group input, .form-group select, .form-group textarea {
                font-size: 14px;
                padding: 12px;
            }
            .btn {
                font-size: 14px;
                padding: 10px 20px;
            }
        }
    </style>
</head>
<body>

    <!-- Слева боковое меню -->
    <div class="sidebar">
        <h2>Админка</h2>
        <ul>
            <li><a href="#">Главная</a></li>
            <li><a href="#">Пользователи</a></li>
            <li><a href="#">Настройки</a></li>
            <li><a href="#">Отчеты</a></li>
            <li><a href="#">Выход</a></li>
        </ul>
    </div>

    <!-- Контент -->
    <div class="content">
        <h1>Добро пожаловать в админку</h1>
        <p>Здесь вы можете управлять всеми аспектами вашего сайта.</p>

        <div class="form-container">
            <form action="{{route('search')}}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="name">Поиск</label>
                    <input type="text" id="query" name="query" placeholder="Введите слово или выражение">
                </div>
                <div class="form-group">
                    <button type="submit" class="btn">Искать</button>
                </div>
            </form>
        </div>

        {{$dictionary->links()}}

        <!-- Таблица -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Слово</th>
                    <th>Перевод</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dictionary as $word)
                <tr>
                    <td>{{$word->id}}</td>
                    <td>{{$word->word}}</td>
                    <td>{{$word->translation}}</td>
                    <td>
                        <button class="btn">Редактировать</button>
                        <form action="{{route('dictionary.destroy', ['dictionary' => $word])}}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="btn" type="submit">Удалить</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>Пусто</tr>
                @endforelse
            </tbody>
        </table>
        {{$dictionary->links()}}
        <!-- Пример формы -->
        <div class="form-container">
            <h2>Добавить пользователя</h2>
            <form>
                <div class="form-group">
                    <label for="name">Имя</label>
                    <input type="text" id="name" name="name" placeholder="Введите имя">
                </div>
                <div class="form-group">
                    <label for="email">Электронная почта</label>
                    <input type="email" id="email" name="email" placeholder="Введите электронную почту">
                </div>
                <div class="form-group">
                    <label for="status">Статус</label>
                    <select id="status" name="status">
                        <option value="active">Активен</option>
                        <option value="inactive">Неактивен</option>
                    </select>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn">Добавить пользователя</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Необходимые скрипты -->
    <script>
        // В дальнейшем можно добавить дополнительные функциональные элементы JavaScript
    </script>

</body>
</html>
