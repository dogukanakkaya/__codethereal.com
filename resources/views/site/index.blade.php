@extends('site.layouts.base')

@section('content')
    <section class="home-top">
        <div class="center container">
            <div>
                <h3 class="slogan"><span>The Mind is a Far Better Warrior than the Sword!</span></h3>
            </div>
            <div class="d-flex justify-content-center align-items-center flex-lg-row flex-column">
                <div class="choose-category">
                    <a onclick="toggleMultiple(this.nextElementSibling, 'visible', 'opacity-100')"><i class="bi bi-list" aria-hidden="true"></i> Choose Category <i class="bi bi-chevron-down category-arrow r-180"></i></a>
                    <ul class="category-dropdown">
                        <li value="1" class="selected">PHP</li>
                        <li value="2">Javascript</li>
                        <li value="3">CSS</li>
                    </ul>
                </div>
                <div class="search">
                    <i class="bi bi-search icon" aria-hidden="true"></i>
                    <input type="text" placeholder="Search in Education sets, courses or job posts...">
                </div>
                <div class="search-button">
                    <button onclick="return false;runAjaxRequest()"><i class="bi bi-search"></i></button>
                </div>
            </div>
        </div>
    </section>

    <section class="categories container">
        <div class="row">
            <div class="col-md-12 head d-flex justify-content-between align-items-center">
                <h4>{{ $category->title }}</h4>
                <a href="{{ url($category->url) }}" class="ce-btn"><i class="bi bi-list"></i> Show All</a>
            </div>
        </div>
        <div class="row">
            @foreach($categoryItems as $categoryItem)
                <div class="col-md-3">
                    <div class="item">
                        <a href="{{ url($categoryItem->url) }}">
                            <img src="{{ resize($categoryItem->featured_image, 150) }}" alt="">
                            <h5>{{ $categoryItem->title }}</h5>
                        </a>
                        <span>({{ $categoryItem->childrens_count }} lessons)</span>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <section class="container mb-5">
        <div class="row">
            @foreach($cards as $card)
                <div class="col-md-4">
                    <div class="ce-card">
                        <div class="face face1">
                            <div class="content">
                                <img src="{{ resize($card->featured_image, 200) }}">
                                <h3>{{ $card->title }}</h3>
                            </div>
                        </div>
                        <div class="face face2">
                            <div class="content">
                                <p>{{ $card->description }}</p>
                                <a href="{{ url($card->url) }}" class="ce-btn">Read More</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <section class="separate-parallax" style="background-image: url('https://www.codethereal.com/new.codethereal/public/site/images/home-top-middle.png');">
        <div class="overlay flex-column d-flex justify-content-center align-items-center p-3 text-center">
            <h3>Are You Ready to Learn with CodEthereal.com?</h3>
            <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Consequatur, perferendis.</p>
            <a href="#" class="ce-btn"><i class="bi bi-person-plus"></i> Sign Up</a>
        </div>
    </section>

    <section class="container contents">
        <div class="row">
            <div class="col-md-12 head d-flex justify-content-between align-items-center">
                <h4>Popular Contents</h4>
                <button class="ce-btn"><i class="bi bi-list"></i> Show All</button>
            </div>
        </div>
        <div class="row gy-5">
            @for ($i = 0; $i < 9; $i++)
            <div class="col-md-4">
                <div class="card">
                    <span class="date">27 December</span>
                    <div class="image">
                        <a href="#">
                            <img src="{{ asset('site/img/code_382x260.jpg') }}" alt="">
                        </a>
                        <div class="item-overlay">
                            <a href="#"> <i class="bi bi-link-45deg"></i></a>
                        </div>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><a href="#">Real Time Chat with NodeJS & Socket.io</a></h5>
                        <p class="card-text">Lorem ipsum, dolor sit amet consectetur adipisicing elit. Quas, voluptatum?</p>
                        <hr>
                        <div class="d-flex justify-content-between align-items-center card-bottom">
                            <a href="#"><i class="bi bi-pencil"></i> Admin</a>
                            <a href="#"><i class="bi bi-chevron-double-right"> Read More</i></a>
                        </div>
                    </div>
                </div>
            </div>
            @endfor
        </div>
    </section>

    <section class="separate-parallax mt-5 mb-5" style="background-image: url('https://www.codethereal.com/new.codethereal/public/site/images/home-top-middle.png');">
        <div class="overlay flex-column d-flex justify-content-center align-items-center">
            <ul class="d-flex justify-content-evenly w-100 counters">
                <li>
                    <i class="bi bi-person-check"></i>
                    <h4>{{ $userCount }}+</h4>
                    <h4>Student</h4>
                </li>
                <li>
                    <i class="bi bi-bookmark-check"></i>
                    <h4>{{ $categoryItemChildrenSum }}+</h4>
                    <h4>Article</h4>
                </li>
                <li>
                    <i class="bi bi-collection"></i>
                    <h4>{{ $categoryCount }}+</h4>
                    <h4>Collection</h4>
                </li>
                <li>
                    <i class="bi bi-question-diamond"></i>
                    <h4>220+</h4>
                    <h4>Question</h4>
                </li>
            </ul>
        </div>
    </section>

    <section class="container mb-5 job-posts">
        <div class="row">
            <div class="col-12 section-title">
                <h2>Job Posts</h2>
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Vel distinctio aut esse ipsa atque natus?</p>
                <div class="line"></div>
            </div>
            <div class="col-12">
                <table class="table">
                    <tbody>
                    <tr>
                        <td class="logo">
                            <img src="{{ asset('site/img/logo-dark.svg') }}" alt="">
                        </td>
                        <td class="title">
                            <h5><a href="#">ReactJS Developer</a></h5>
                            <span>Codethereal</span>
                        </td>
                        <td class="location">
                            <i class="bi bi-geo-alt"></i> Pendik, İstanbul
                        </td>
                        <td class="type">
                            <span>FULL TIME</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="logo">
                            <img src="{{ asset('site/img/tesla.png') }}" alt="">
                        </td>
                        <td class="title">
                            <h5><a href="#">Python Developer</a></h5>
                            <span>Tesla</span>
                        </td>
                        <td class="location">
                            <i class="bi bi-geo-alt"></i> Pendik, İstanbul
                        </td>
                        <td class="type">
                            <span>PART TIME</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="logo">
                            <img src="{{ asset('site/img/spacex.png') }}" alt="">
                        </td>
                        <td class="title">
                            <h5><a href="#">Sr. AI Expert</a></h5>
                            <span>Space X</span>
                        </td>
                        <td class="location">
                            <i class="bi bi-geo-alt"></i> Pendik, İstanbul
                        </td>
                        <td class="type">
                            <span>FULL TIME</span>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection