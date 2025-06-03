<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialize session array if not set
if (!isset($_SESSION['students'])) {
    $_SESSION['students'] = [];
}

// Handle AJAX Requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'add':
            $id = time() . rand(1000, 9999);
            $_SESSION['students'][$id] = [
                'id' => $id,
                'name' => $_POST['name'],
                'age' => intval($_POST['age']),
                'class' => $_POST['class'],
            ];
            break;
        case 'update':
            $id = $_POST['id'];
            if (isset($_SESSION['students'][$id])) {
                $_SESSION['students'][$id]['name'] = $_POST['name'];
                $_SESSION['students'][$id]['age'] = intval($_POST['age']);
                $_SESSION['students'][$id]['class'] = $_POST['class'];
            }
            break;
        case 'delete':
            $id = $_POST['id'];
            unset($_SESSION['students'][$id]);
            break;
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['action'] === 'get_students') {
    header('Content-Type: application/json');
    echo json_encode(array_values($_SESSION['students']));
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Student Management</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">

  <header class="bg-green-700 text-white px-6 py-4 shadow-md">
    <h1 class="text-3xl font-bold text-center">📚 Student Management System</h1>
  </header>

  <main class="flex flex-col md:flex-row gap-8 p-6">
    <!-- Form Panel -->
    <section class="md:w-1/3 bg-white shadow-md rounded-xl p-6">
      <h2 class="text-xl font-semibold mb-4 text-center" id="formTitle">➕ Add Student</h2>
      <form id="addStudentForm" class="space-y-4">
        <div>
          <label class="block text-gray-700 font-medium mb-1">Name</label>
          <input type="text" name="name" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-400" />
        </div>
        <div>
          <label class="block text-gray-700 font-medium mb-1">Age</label>
          <input type="number" name="age" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-400" />
        </div>
        <div>
          <label class="block text-gray-700 font-medium mb-1">Class</label>
          <input type="text" name="class" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-400" />
        </div>
        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 rounded-md mt-2">
          Add Student
        </button>
      </form>
    </section>

    <!-- Student List Panel -->
    <section class="md:flex-1 bg-white shadow-md rounded-xl p-6">
      <h2 class="text-xl font-semibold mb-6 text-center">📋 Student List</h2>
      <div id="studentCards" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4"></div>
    </section>
  </main>

  <script>
    let editingId = null;
    window.students = [];

    async function fetchStudents() {
      const res = await fetch("index.php?action=get_students");
      const data = await res.json();
      window.students = data;

      const container = document.getElementById("studentCards");
      container.innerHTML = "";

      data.forEach((student) => {
        container.innerHTML += `
          <div class="border rounded-lg p-4 shadow-sm bg-gray-50 relative">
            <h3 class="text-lg font-semibold text-green-700 mb-2">${student.name}</h3>
            <p class="text-sm text-gray-600">Age: ${student.age}</p>
            <p class="text-sm text-gray-600">Class: ${student.class}</p>
            <div class="flex gap-2 mt-4">
              <button onclick="editStudent(${student.id})" class="flex-1 bg-blue-500 hover:bg-blue-600 text-white py-1 rounded-md">Edit</button>
              <button onclick="deleteStudent(${student.id})" class="flex-1 bg-red-500 hover:bg-red-600 text-white py-1 rounded-md">Delete</button>
            </div>
          </div>
        `;
      });
    }

    function editStudent(id) {
      const student = window.students.find((s) => s.id == id);
      if (!student) return;

      editingId = id;
      const form = document.getElementById("addStudentForm");
      form.name.value = student.name;
      form.age.value = student.age;
      form.class.value = student.class;

      form.querySelector('button[type="submit"]').textContent = "Update Student";
      document.getElementById("formTitle").textContent = "✏️ Edit Student";
    }

    async function deleteStudent(id) {
      await fetch("index.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `action=delete&id=${id}`,
      });
      fetchStudents();
    }

    document.getElementById("addStudentForm").addEventListener("submit", async (e) => {
      e.preventDefault();
      const formData = new FormData(e.target);
      const action = editingId ? "update" : "add";
      formData.append("action", action);
      if (editingId) {
        formData.append("id", editingId);
      }

      await fetch("index.php", {
        method: "POST",
        body: new URLSearchParams(formData),
      });

      e.target.reset();
      editingId = null;
      e.target.querySelector('button[type="submit"]').textContent = "Add Student";
      document.getElementById("formTitle").textContent = "➕ Add Student";
      fetchStudents();
    });

    fetchStudents();
  </script>
</body>
</html>
