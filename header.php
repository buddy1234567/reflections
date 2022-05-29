<nav class="navbar navbar-expand-lg navbar-dark bg-dark" aria-label="Eighth navbar example">
    <div class="container">
      <a class="navbar-brand" href="index.php">Reflections</a> 
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExample07" aria-controls="navbarsExample07" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarsExample07">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="#">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="classes.php">Classes</a>
          </li>
          <?php if(ISSET($_SESSION['username'])) { if($_SESSION['level'] < 3) { ?>
          <li class="nav-item">
            <a class="nav-link" href="students.php">Students</a>
          </li>
          <?php }  } ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="dropdown07" data-bs-toggle="dropdown" aria-expanded="false">Options</a>
            <ul class="dropdown-menu" aria-labelledby="dropdown07">
              <li><a class="dropdown-item" href="#">Action</a></li>
              <li><a class="dropdown-item" href="#">Another action</a></li>
              <li><a class="dropdown-item" href="#">Something else here</a></li>
            </ul>
          </li>
        </ul>
      </div>
      <div class="text-end">
        <?php
            if(ISSET($_SESSION['username'])) {
              echo '<a href="logout.php"  class="btn btn-outline-light me-2">Logout</a>';         
            } else {
                echo '<a href="login.php"  class="btn btn-outline-light me-2">Login</a>
                <a href="register.php" class="btn btn-outline-warning me-2">Sign-up</a>';
            }
        ?>
        </div>
    </div>
  </nav>